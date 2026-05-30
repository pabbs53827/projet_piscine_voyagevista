import { useEffect, useState } from 'react';
import { fetchDestinations } from '../services/api';
import DestinationCard from '../components/DestinationCard';

export default function Home() {

    const [destinations, setDestinations] = useState([]);
    const [loading, setLoading] = useState(true);
    const [error, setError] = useState(null);

    useEffect(() => {
        setLoading(true);
        setError(null);

        fetchDestinations()
            .then(data => setDestinations(data))
            .catch(err => setError(err.message))
            .finally(() => setLoading(false));
    }, []);

    return (
        <main>

            {/* ── HERO ── */}
            <section className="hero">
                <div className="hero-bg">
                    <div className="blob blob-1" />
                    <div className="blob blob-2" />
                    <div className="blob blob-3" />
                    <span className="float-star s1">✦</span>
                    <span className="float-star s2">✦</span>
                    <span className="float-star s3">✦</span>
                    <span className="float-pill p1">🚂 Trains & ferries</span>
                    <span className="float-pill p2">🏡 Hébergements locaux</span>
                    <span className="float-pill p3">🌿 Expériences locales</span>
                </div>

                <div className="hero-content">
                    <div className="hero-badge">✦ Slow travel · Depuis 2026</div>
                    <h1 className="hero-title">
                        Prenez<br />
                        <em className="hero-accent">le temps</em><br />
                        du voyage.
                    </h1>
                    <p className="hero-sub">
                        Trains, ferries, gîtes et expériences locales —
                        pour ceux qui veulent voir le monde autrement.
                    </p>

                    <div className="search-card">
                        <div className="sg">
                            <label htmlFor="sf-dest">Destination</label>
                            <input id="sf-dest" type="text" placeholder="Drôme, Piémont, côte dalmate..." />
                        </div>
                        <div className="sd" />
                        <div className="sg">
                            <label htmlFor="sf-type">Type de voyage</label>
                            <select id="sf-type">
                                <option value="">Tous types</option>
                                <option value="campagne">Campagne &amp; nature</option>
                                <option value="cote">Côte &amp; village marin</option>
                                <option value="montagne">Montagne</option>
                                <option value="village">Village</option>
                                <option value="culture">Culture &amp; patrimoine</option>
                            </select>
                        </div>
                        <div className="sd" />
                        <button className="btn btn-search">Trouver un séjour</button>
                    </div>

                    <div className="hero-tags">
                        <span className="htag">🚂 Trains &amp; ferries</span>
                        <span className="htag">🏡 Hébergements locaux</span>
                        <span className="htag">🌿 Expériences</span>
                        <span className="htag">🗓️ Votre rythme</span>
                    </div>
                </div>
            </section>

            {/* ── DESTINATIONS ── */}
            <section className="reveal-section h-destinations" id="destinations">
                <div className="h-container">
                    <div className="h-head">
                        <span className="h-tag">Destinations</span>
                        <h2>Là où l'on voyage lentement</h2>
                        <p>Des endroits choisis pour leur caractère, pas pour leur fréquentation.</p>
                    </div>

                    {loading && (
                        <div className="state-msg">Chargement des destinations...</div>
                    )}

                    {error && (
                        <div className="state-msg state-error">
                            ⚠️ {error} — vérifiez que WampServer tourne.
                        </div>
                    )}

                    {!loading && !error && destinations.length === 0 && (
                        <div className="state-msg">Aucune destination trouvée.</div>
                    )}

                    {!loading && !error && destinations.length > 0 && (
                        <div className="dest-grid">
                            {destinations.map(dest => (
                                <DestinationCard key={dest.id_destination} destination={dest} />
                            ))}
                        </div>
                    )}
                </div>
            </section>

            {/* ── COMMENT ÇA MARCHE ── */}
            <section className="reveal-section h-whyus" id="comment-ca-marche">
                <div className="h-container">
                    <div className="h-head">
                        <span className="h-tag">Le principe</span>
                        <h2>Voyager autrement, c'est simple</h2>
                    </div>
                    <div className="bento-grid">
                        {[
                            { icon: '🚂', title: 'Trains & ferries',        desc: 'Voyages lents, paysages inclus.',          cls: 'bento-pink'     },
                            { icon: '🏡', title: 'Hébergements locaux',      desc: "Gîtes, ch. d'hôte, auberges.",             cls: 'bento-yellow'   },
                            { icon: '🌿', title: 'Expériences authentiques', desc: 'Rencontres, artisanat, terroir.',           cls: 'bento-mint'     },
                            { icon: '🗓️', title: 'Votre rythme',             desc: "Pas d'itinéraire imposé.",                 cls: 'bento-lavender' },
                        ].map(b => (
                            <div className={'bento ' + b.cls} key={b.title}>
                                <span className="bento-icon">{b.icon}</span>
                                <h3>{b.title}</h3>
                                <p>{b.desc}</p>
                            </div>
                        ))}
                    </div>
                </div>
            </section>

        </main>
    );
}
