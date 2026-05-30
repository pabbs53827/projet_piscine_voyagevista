export default function DestinationCard({ destination }) {
    const { id_destination, nom, pays, region, description, image_url } = destination;

    return (
        <div className="dcard">
            <div className="dcard-img">
                {image_url ? (
                    <img
                        src={image_url}
                        alt={nom}
                        className="dcard-photo"
                    />
                ) : (
                    <div className="dcard-placeholder" />
                )}
                {region && (
                    <span className="dcard-tag" style={{ background: '#FFE4EF', color: '#FF4D8D' }}>
                        {region}
                    </span>
                )}
            </div>

            <div className="dcard-body">
                <p className="dcard-loc">{pays}</p>
                <h3 className="dcard-name">{nom}</h3>
                <p className="dcard-desc">{description}</p>

                <div className="dcard-footer">
                    <a href={'/destinations/' + id_destination} className="btn btn-lagoon btn-sm">
                        Découvrir
                    </a>
                </div>
            </div>
        </div>
    );
}
