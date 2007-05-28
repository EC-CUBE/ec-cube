CREATE TABLE dtb_mobile_ext_session_id (
    session_id text NOT NULL,
    param_key text,
    param_value text,
    url text,
    create_date timestamp NOT NULL DEFAULT now()
);

CREATE INDEX dtb_mobile_ext_session_id_param_key_key ON dtb_mobile_ext_session_id (param_key(64));
CREATE INDEX dtb_mobile_ext_session_id_param_value_key ON dtb_mobile_ext_session_id (param_value(64));
CREATE INDEX dtb_mobile_ext_session_id_url_key ON dtb_mobile_ext_session_id (url(64));
CREATE INDEX dtb_mobile_ext_session_id_create_date_key ON dtb_mobile_ext_session_id (create_date);

