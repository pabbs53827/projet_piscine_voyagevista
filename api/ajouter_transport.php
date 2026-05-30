<?php
declare(strict_types=1);

// POST { id_sejour, id_transport, sens, date_depart, date_arrivee, nb_passagers }
// aller  : utilise le transport existant, décrémente places_dispo 
// retour : crée un transport à la volée (même route inversée)
require __DIR__ . '/_helpers.php';
require __DIR__ . '/_db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') json_error('Méthode non autorisée.', 405);

$user = require_auth();
$body = read_json_body();

$idSejour    = isset($body['id_sejour'])    ? (int)$body['id_sejour']                                        : 0;
$idTransport = isset($body['id_transport']) ? (int)$body['id_transport']                                     : 0;
$sens        = in_array($body['sens'] ?? '', ['aller','retour'], true) ? $body['sens']                       : 'aller';
$dateDepart  = isset($body['date_depart'])  ? trim($body['date_depart'])                                     : '';
$dateArrivee = isset($body['date_arrivee']) ? trim($body['date_arrivee'])                                    : '';
$nbPass      = isset($body['nb_passagers']) ? max(1, (int)$body['nb_passagers'])                            : 1;

if ($idSejour <= 0 || $idTransport <= 0 || !$dateDepart || !$dateArrivee) {
    json_error('Données manquantes ou invalides.');
}
if ($dateArrivee <= $dateDepart) json_error('La date d\'arrivée doit être après le départ.');

$pdo = db();

$stmt = $pdo->prepare('SELECT id_sejour FROM sejour WHERE id_sejour = ? AND id_voyageur = ? AND statut = "brouillon"');
$stmt->execute([$idSejour, $user['id']]);
if (!$stmt->fetch()) json_error('Séjour introuvable.', 404);

$stmt = $pdo->prepare('SELECT * FROM transport WHERE id_transport = ?');
$stmt->execute([$idTransport]);
$transport = $stmt->fetch();
if (!$transport) json_error('Transport introuvable.', 404);

$pdo->beginTransaction();
try {
    if ($sens === 'aller') {
        $stmt = $pdo->prepare('SELECT * FROM transport WHERE id_transport = ? AND places_dispo >= ? FOR UPDATE');
        $stmt->execute([$idTransport, $nbPass]);
        $t = $stmt->fetch();
        if (!$t) { $pdo->rollBack(); json_error('Plus assez de places disponibles.', 409); }

        $prixUnit       = (float)$t['prix'];
        $finalTransport = $idTransport;

        $pdo->prepare('UPDATE transport SET places_dispo = places_dispo - ? WHERE id_transport = ?')
            ->execute([$nbPass, $idTransport]);
    } else {
        // Retour : crée un enregistrement on-the-fly.
        // ville_depart copié de l'original, id_destination_arr identique →
        // le panier affiche "destination → ville_depart" grâce au champ sens.
        $prixUnit = (float)$transport['prix'];

        $pdo->prepare(
            'INSERT INTO transport (id_destination_arr, ville_depart, type, compagnie, date_depart, date_arrivee, prix, places_dispo)
             VALUES (?, ?, ?, NULL, ?, ?, ?, 0)'
        )->execute([
            $transport['id_destination_arr'],
            $transport['ville_depart'],
            $transport['type'],
            $dateDepart,
            $dateArrivee,
            $prixUnit,
        ]);
        $finalTransport = (int)$pdo->lastInsertId();
    }

    $prix = round($nbPass * $prixUnit, 2);

    $pdo->prepare('INSERT INTO sejour_transport (id_sejour, id_transport, nb_passagers, sens, prix) VALUES (?, ?, ?, ?, ?)')
        ->execute([$idSejour, $finalTransport, $nbPass, $sens, $prix]);
    $pdo->prepare('UPDATE sejour SET prix_total = prix_total + ? WHERE id_sejour = ?')
        ->execute([$prix, $idSejour]);

    $pdo->commit();
    json_response(['message' => 'Transport ajouté à votre séjour.', 'prix' => $prix, 'sens' => $sens]);
} catch (Throwable $e) {
    $pdo->rollBack();
    throw $e;
}
