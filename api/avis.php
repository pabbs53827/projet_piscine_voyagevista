<?php
declare(strict_types=1);

// GET  ?type=hebergement&id=5   → note moyenne + liste avis
// POST { type, id_cible, note, commentaire }  → soumettre un avis
require __DIR__ . '/_helpers.php';
require __DIR__ . '/_db.php';

$pdo = db();

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $type = isset($_GET['type']) ? trim($_GET['type']) : '';
    $id   = isset($_GET['id'])   ? (int)$_GET['id']   : 0;
    if (!in_array($type, ['hebergement','activite'], true) || $id <= 0) {
        json_error('Paramètres invalides.');
    }

    $stmt = $pdo->prepare(
        'SELECT a.note, a.commentaire, a.date_avis,
                u.prenom, u.nom
         FROM avis a
         JOIN utilisateur u ON u.id_utilisateur = a.id_voyageur
         WHERE a.type_cible = ? AND a.id_cible = ?
         ORDER BY a.date_avis DESC
         LIMIT 20'
    );
    $stmt->execute([$type, $id]);
    $avis = $stmt->fetchAll();

    $avg = null;
    if (count($avis) > 0) {
        $avg = round(array_sum(array_column($avis, 'note')) / count($avis), 1);
    }

    json_response(['avis' => $avis, 'moyenne' => $avg, 'total' => count($avis)]);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user = require_auth();
    $body = read_json_body();

    $type        = isset($body['type'])         ? trim($body['type'])          : '';
    $idCible     = isset($body['id_cible'])     ? (int)$body['id_cible']       : 0;
    $note        = isset($body['note'])         ? (int)$body['note']           : 0;
    $commentaire = isset($body['commentaire'])  ? trim($body['commentaire'])   : '';

    if (!in_array($type, ['hebergement','activite'], true) || $idCible <= 0) {
        json_error('Paramètres invalides.');
    }
    if ($note < 1 || $note > 5) json_error('La note doit être entre 1 et 5.');

    // Vérifie que l'utilisateur a bien eu ce séjour validé avec cet élément
    if ($type === 'hebergement') {
        $stmt = $pdo->prepare(
            'SELECT 1 FROM sejour_hebergement sh
             JOIN sejour s ON s.id_sejour = sh.id_sejour
             JOIN disponibilite dp ON dp.id_dispo = sh.id_dispo
             WHERE s.id_voyageur = ? AND s.statut = "valide"
               AND dp.id_hebergement = ?
             LIMIT 1'
        );
    } else {
        $stmt = $pdo->prepare(
            'SELECT 1 FROM sejour_activite sa
             JOIN sejour s ON s.id_sejour = sa.id_sejour
             JOIN creneau c ON c.id_creneau = sa.id_creneau
             WHERE s.id_voyageur = ? AND s.statut = "valide"
               AND c.id_activite = ?
             LIMIT 1'
        );
    }
    $stmt->execute([$user['id'], $idCible]);
    if (!$stmt->fetch()) json_error('Vous devez avoir effectué ce séjour pour laisser un avis.', 403);

    // Un seul avis par utilisateur par cible
    $stmt = $pdo->prepare('SELECT id_avis FROM avis WHERE id_voyageur = ? AND type_cible = ? AND id_cible = ?');
    $stmt->execute([$user['id'], $type, $idCible]);
    if ($stmt->fetch()) json_error('Vous avez déjà laissé un avis pour cet élément.', 409);

    $pdo->prepare(
        'INSERT INTO avis (id_voyageur, type_cible, id_cible, note, commentaire) VALUES (?, ?, ?, ?, ?)'
    )->execute([$user['id'], $type, $idCible, $note, $commentaire ?: null]);

    json_response(['message' => 'Avis enregistré. Merci !'], 201);
}

json_error('Méthode non autorisée.', 405);
