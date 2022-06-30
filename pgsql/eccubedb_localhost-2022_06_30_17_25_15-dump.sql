--
-- PostgreSQL database dump
--

-- Dumped from database version 14.3 (Debian 14.3-1.pgdg110+1)
-- Dumped by pg_dump version 14.3

SET statement_timeout = 0;
SET lock_timeout = 0;
SET idle_in_transaction_session_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SELECT pg_catalog.set_config('search_path', '', false);
SET check_function_bodies = false;
SET xmloption = content;
SET client_min_messages = warning;
SET row_security = off;

\connect postgres
--
-- Name: eccubedb; Type: DATABASE; Schema: -; Owner: dbuser
--
DROP DATABASE eccubedb;
CREATE DATABASE eccubedb WITH TEMPLATE = template0 ENCODING = 'UTF8' LOCALE = 'en_US.utf8';


ALTER DATABASE eccubedb OWNER TO dbuser;

\connect eccubedb

SET statement_timeout = 0;
SET lock_timeout = 0;
SET idle_in_transaction_session_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SELECT pg_catalog.set_config('search_path', '', false);
SET check_function_bodies = false;
SET xmloption = content;
SET client_min_messages = warning;
SET row_security = off;

--
-- Name: public; Type: SCHEMA; Schema: -; Owner: dbuser
--
DROP SCHEMA public;

CREATE SCHEMA public;

ALTER SCHEMA public OWNER TO dbuser;

--
-- Name: SCHEMA public; Type: COMMENT; Schema: -; Owner: dbuser
--

COMMENT ON SCHEMA public IS 'standard public schema';


SET default_tablespace = '';

SET default_table_access_method = heap;

--
-- Name: dtb_authority_role; Type: TABLE; Schema: public; Owner: dbuser
--

CREATE TABLE public.dtb_authority_role (
    id integer NOT NULL,
    authority_id smallint,
    creator_id integer,
    deny_url character varying(4000) NOT NULL,
    create_date timestamp(0) with time zone NOT NULL,
    update_date timestamp(0) with time zone NOT NULL,
    discriminator_type character varying(255) NOT NULL
);


ALTER TABLE public.dtb_authority_role OWNER TO dbuser;

--
-- Name: COLUMN dtb_authority_role.create_date; Type: COMMENT; Schema: public; Owner: dbuser
--

COMMENT ON COLUMN public.dtb_authority_role.create_date IS '(DC2Type:datetimetz)';


--
-- Name: COLUMN dtb_authority_role.update_date; Type: COMMENT; Schema: public; Owner: dbuser
--

COMMENT ON COLUMN public.dtb_authority_role.update_date IS '(DC2Type:datetimetz)';


--
-- Name: dtb_authority_role_id_seq; Type: SEQUENCE; Schema: public; Owner: dbuser
--

CREATE SEQUENCE public.dtb_authority_role_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.dtb_authority_role_id_seq OWNER TO dbuser;

--
-- Name: dtb_authority_role_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: dbuser
--

ALTER SEQUENCE public.dtb_authority_role_id_seq OWNED BY public.dtb_authority_role.id;


--
-- Name: dtb_base_info; Type: TABLE; Schema: public; Owner: dbuser
--

CREATE TABLE public.dtb_base_info (
    id integer NOT NULL,
    country_id smallint,
    pref_id smallint,
    company_name character varying(255) DEFAULT NULL::character varying,
    company_kana character varying(255) DEFAULT NULL::character varying,
    postal_code character varying(8) DEFAULT NULL::character varying,
    addr01 character varying(255) DEFAULT NULL::character varying,
    addr02 character varying(255) DEFAULT NULL::character varying,
    phone_number character varying(14) DEFAULT NULL::character varying,
    business_hour character varying(255) DEFAULT NULL::character varying,
    email01 character varying(255) DEFAULT NULL::character varying,
    email02 character varying(255) DEFAULT NULL::character varying,
    email03 character varying(255) DEFAULT NULL::character varying,
    email04 character varying(255) DEFAULT NULL::character varying,
    shop_name character varying(255) DEFAULT NULL::character varying,
    shop_kana character varying(255) DEFAULT NULL::character varying,
    shop_name_eng character varying(255) DEFAULT NULL::character varying,
    update_date timestamp(0) with time zone NOT NULL,
    good_traded character varying(4000) DEFAULT NULL::character varying,
    message character varying(4000) DEFAULT NULL::character varying,
    delivery_free_amount numeric(12,2) DEFAULT NULL::numeric,
    delivery_free_quantity integer,
    option_mypage_order_status_display boolean DEFAULT true NOT NULL,
    option_nostock_hidden boolean DEFAULT false NOT NULL,
    option_favorite_product boolean DEFAULT true NOT NULL,
    option_product_delivery_fee boolean DEFAULT false NOT NULL,
    option_product_tax_rule boolean DEFAULT false NOT NULL,
    option_customer_activate boolean DEFAULT true NOT NULL,
    invoice_registration_number character varying(255) DEFAULT NULL::character varying,
    option_remember_me boolean DEFAULT true NOT NULL,
    authentication_key character varying(255) DEFAULT NULL::character varying,
    php_path character varying(255) DEFAULT NULL::character varying,
    option_point boolean DEFAULT true NOT NULL,
    basic_point_rate numeric(10,0) DEFAULT '1'::numeric,
    point_conversion_rate numeric(10,0) DEFAULT '1'::numeric,
    discriminator_type character varying(255) NOT NULL
);


ALTER TABLE public.dtb_base_info OWNER TO dbuser;

--
-- Name: COLUMN dtb_base_info.update_date; Type: COMMENT; Schema: public; Owner: dbuser
--

COMMENT ON COLUMN public.dtb_base_info.update_date IS '(DC2Type:datetimetz)';


--
-- Name: dtb_base_info_id_seq; Type: SEQUENCE; Schema: public; Owner: dbuser
--

CREATE SEQUENCE public.dtb_base_info_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.dtb_base_info_id_seq OWNER TO dbuser;

--
-- Name: dtb_base_info_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: dbuser
--

ALTER SEQUENCE public.dtb_base_info_id_seq OWNED BY public.dtb_base_info.id;


--
-- Name: dtb_block; Type: TABLE; Schema: public; Owner: dbuser
--

CREATE TABLE public.dtb_block (
    id integer NOT NULL,
    device_type_id smallint,
    block_name character varying(255) DEFAULT NULL::character varying,
    file_name character varying(255) NOT NULL,
    use_controller boolean DEFAULT false NOT NULL,
    deletable boolean DEFAULT true NOT NULL,
    create_date timestamp(0) with time zone NOT NULL,
    update_date timestamp(0) with time zone NOT NULL,
    discriminator_type character varying(255) NOT NULL
);


ALTER TABLE public.dtb_block OWNER TO dbuser;

--
-- Name: COLUMN dtb_block.create_date; Type: COMMENT; Schema: public; Owner: dbuser
--

COMMENT ON COLUMN public.dtb_block.create_date IS '(DC2Type:datetimetz)';


--
-- Name: COLUMN dtb_block.update_date; Type: COMMENT; Schema: public; Owner: dbuser
--

COMMENT ON COLUMN public.dtb_block.update_date IS '(DC2Type:datetimetz)';


--
-- Name: dtb_block_id_seq; Type: SEQUENCE; Schema: public; Owner: dbuser
--

CREATE SEQUENCE public.dtb_block_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.dtb_block_id_seq OWNER TO dbuser;

--
-- Name: dtb_block_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: dbuser
--

ALTER SEQUENCE public.dtb_block_id_seq OWNED BY public.dtb_block.id;


--
-- Name: dtb_block_position; Type: TABLE; Schema: public; Owner: dbuser
--

CREATE TABLE public.dtb_block_position (
    section integer NOT NULL,
    block_id integer NOT NULL,
    layout_id integer NOT NULL,
    block_row integer,
    discriminator_type character varying(255) NOT NULL
);


ALTER TABLE public.dtb_block_position OWNER TO dbuser;

--
-- Name: dtb_calendar; Type: TABLE; Schema: public; Owner: dbuser
--

CREATE TABLE public.dtb_calendar (
    id integer NOT NULL,
    title character varying(255) DEFAULT NULL::character varying,
    holiday timestamp(0) with time zone NOT NULL,
    create_date timestamp(0) with time zone NOT NULL,
    update_date timestamp(0) with time zone NOT NULL,
    discriminator_type character varying(255) NOT NULL
);


ALTER TABLE public.dtb_calendar OWNER TO dbuser;

--
-- Name: COLUMN dtb_calendar.holiday; Type: COMMENT; Schema: public; Owner: dbuser
--

COMMENT ON COLUMN public.dtb_calendar.holiday IS '(DC2Type:datetimetz)';


--
-- Name: COLUMN dtb_calendar.create_date; Type: COMMENT; Schema: public; Owner: dbuser
--

COMMENT ON COLUMN public.dtb_calendar.create_date IS '(DC2Type:datetimetz)';


--
-- Name: COLUMN dtb_calendar.update_date; Type: COMMENT; Schema: public; Owner: dbuser
--

COMMENT ON COLUMN public.dtb_calendar.update_date IS '(DC2Type:datetimetz)';


--
-- Name: dtb_calendar_id_seq; Type: SEQUENCE; Schema: public; Owner: dbuser
--

CREATE SEQUENCE public.dtb_calendar_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.dtb_calendar_id_seq OWNER TO dbuser;

--
-- Name: dtb_calendar_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: dbuser
--

ALTER SEQUENCE public.dtb_calendar_id_seq OWNED BY public.dtb_calendar.id;


--
-- Name: dtb_cart; Type: TABLE; Schema: public; Owner: dbuser
--

CREATE TABLE public.dtb_cart (
    id integer NOT NULL,
    customer_id integer,
    cart_key character varying(255) DEFAULT NULL::character varying,
    pre_order_id character varying(255) DEFAULT NULL::character varying,
    total_price numeric(12,2) DEFAULT '0'::numeric NOT NULL,
    delivery_fee_total numeric(12,2) DEFAULT '0'::numeric NOT NULL,
    sort_no smallint,
    create_date timestamp(0) with time zone NOT NULL,
    update_date timestamp(0) with time zone NOT NULL,
    add_point numeric(12,0) DEFAULT '0'::numeric NOT NULL,
    use_point numeric(12,0) DEFAULT '0'::numeric NOT NULL,
    discriminator_type character varying(255) NOT NULL
);


ALTER TABLE public.dtb_cart OWNER TO dbuser;

--
-- Name: COLUMN dtb_cart.create_date; Type: COMMENT; Schema: public; Owner: dbuser
--

COMMENT ON COLUMN public.dtb_cart.create_date IS '(DC2Type:datetimetz)';


--
-- Name: COLUMN dtb_cart.update_date; Type: COMMENT; Schema: public; Owner: dbuser
--

COMMENT ON COLUMN public.dtb_cart.update_date IS '(DC2Type:datetimetz)';


--
-- Name: dtb_cart_id_seq; Type: SEQUENCE; Schema: public; Owner: dbuser
--

CREATE SEQUENCE public.dtb_cart_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.dtb_cart_id_seq OWNER TO dbuser;

--
-- Name: dtb_cart_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: dbuser
--

ALTER SEQUENCE public.dtb_cart_id_seq OWNED BY public.dtb_cart.id;


--
-- Name: dtb_cart_item; Type: TABLE; Schema: public; Owner: dbuser
--

CREATE TABLE public.dtb_cart_item (
    id integer NOT NULL,
    product_class_id integer,
    cart_id integer,
    price numeric(12,2) DEFAULT '0'::numeric NOT NULL,
    quantity numeric(10,0) DEFAULT '0'::numeric NOT NULL,
    point_rate numeric(10,0) DEFAULT NULL::numeric,
    discriminator_type character varying(255) NOT NULL
);


ALTER TABLE public.dtb_cart_item OWNER TO dbuser;

--
-- Name: dtb_cart_item_id_seq; Type: SEQUENCE; Schema: public; Owner: dbuser
--

CREATE SEQUENCE public.dtb_cart_item_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.dtb_cart_item_id_seq OWNER TO dbuser;

--
-- Name: dtb_cart_item_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: dbuser
--

ALTER SEQUENCE public.dtb_cart_item_id_seq OWNED BY public.dtb_cart_item.id;


--
-- Name: dtb_category; Type: TABLE; Schema: public; Owner: dbuser
--

CREATE TABLE public.dtb_category (
    id integer NOT NULL,
    parent_category_id integer,
    creator_id integer,
    category_name character varying(255) NOT NULL,
    hierarchy integer NOT NULL,
    sort_no integer NOT NULL,
    create_date timestamp(0) with time zone NOT NULL,
    update_date timestamp(0) with time zone NOT NULL,
    discriminator_type character varying(255) NOT NULL
);


ALTER TABLE public.dtb_category OWNER TO dbuser;

--
-- Name: COLUMN dtb_category.create_date; Type: COMMENT; Schema: public; Owner: dbuser
--

COMMENT ON COLUMN public.dtb_category.create_date IS '(DC2Type:datetimetz)';


--
-- Name: COLUMN dtb_category.update_date; Type: COMMENT; Schema: public; Owner: dbuser
--

COMMENT ON COLUMN public.dtb_category.update_date IS '(DC2Type:datetimetz)';


--
-- Name: dtb_category_id_seq; Type: SEQUENCE; Schema: public; Owner: dbuser
--

CREATE SEQUENCE public.dtb_category_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.dtb_category_id_seq OWNER TO dbuser;

--
-- Name: dtb_category_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: dbuser
--

ALTER SEQUENCE public.dtb_category_id_seq OWNED BY public.dtb_category.id;


--
-- Name: dtb_class_category; Type: TABLE; Schema: public; Owner: dbuser
--

CREATE TABLE public.dtb_class_category (
    id integer NOT NULL,
    class_name_id integer,
    creator_id integer,
    backend_name character varying(255) DEFAULT NULL::character varying,
    name character varying(255) NOT NULL,
    sort_no integer NOT NULL,
    visible boolean DEFAULT true NOT NULL,
    create_date timestamp(0) with time zone NOT NULL,
    update_date timestamp(0) with time zone NOT NULL,
    discriminator_type character varying(255) NOT NULL
);


ALTER TABLE public.dtb_class_category OWNER TO dbuser;

--
-- Name: COLUMN dtb_class_category.create_date; Type: COMMENT; Schema: public; Owner: dbuser
--

COMMENT ON COLUMN public.dtb_class_category.create_date IS '(DC2Type:datetimetz)';


--
-- Name: COLUMN dtb_class_category.update_date; Type: COMMENT; Schema: public; Owner: dbuser
--

COMMENT ON COLUMN public.dtb_class_category.update_date IS '(DC2Type:datetimetz)';


--
-- Name: dtb_class_category_id_seq; Type: SEQUENCE; Schema: public; Owner: dbuser
--

CREATE SEQUENCE public.dtb_class_category_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.dtb_class_category_id_seq OWNER TO dbuser;

--
-- Name: dtb_class_category_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: dbuser
--

ALTER SEQUENCE public.dtb_class_category_id_seq OWNED BY public.dtb_class_category.id;


--
-- Name: dtb_class_name; Type: TABLE; Schema: public; Owner: dbuser
--

CREATE TABLE public.dtb_class_name (
    id integer NOT NULL,
    creator_id integer,
    backend_name character varying(255) DEFAULT NULL::character varying,
    name character varying(255) NOT NULL,
    sort_no integer NOT NULL,
    create_date timestamp(0) with time zone NOT NULL,
    update_date timestamp(0) with time zone NOT NULL,
    discriminator_type character varying(255) NOT NULL
);


ALTER TABLE public.dtb_class_name OWNER TO dbuser;

--
-- Name: COLUMN dtb_class_name.create_date; Type: COMMENT; Schema: public; Owner: dbuser
--

COMMENT ON COLUMN public.dtb_class_name.create_date IS '(DC2Type:datetimetz)';


--
-- Name: COLUMN dtb_class_name.update_date; Type: COMMENT; Schema: public; Owner: dbuser
--

COMMENT ON COLUMN public.dtb_class_name.update_date IS '(DC2Type:datetimetz)';


--
-- Name: dtb_class_name_id_seq; Type: SEQUENCE; Schema: public; Owner: dbuser
--

CREATE SEQUENCE public.dtb_class_name_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.dtb_class_name_id_seq OWNER TO dbuser;

--
-- Name: dtb_class_name_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: dbuser
--

ALTER SEQUENCE public.dtb_class_name_id_seq OWNED BY public.dtb_class_name.id;


--
-- Name: dtb_csv; Type: TABLE; Schema: public; Owner: dbuser
--

CREATE TABLE public.dtb_csv (
    id integer NOT NULL,
    csv_type_id smallint,
    creator_id integer,
    entity_name character varying(255) NOT NULL,
    field_name character varying(255) NOT NULL,
    reference_field_name character varying(255) DEFAULT NULL::character varying,
    disp_name character varying(255) NOT NULL,
    sort_no smallint NOT NULL,
    enabled boolean DEFAULT true NOT NULL,
    create_date timestamp(0) with time zone NOT NULL,
    update_date timestamp(0) with time zone NOT NULL,
    discriminator_type character varying(255) NOT NULL
);


ALTER TABLE public.dtb_csv OWNER TO dbuser;

--
-- Name: COLUMN dtb_csv.create_date; Type: COMMENT; Schema: public; Owner: dbuser
--

COMMENT ON COLUMN public.dtb_csv.create_date IS '(DC2Type:datetimetz)';


--
-- Name: COLUMN dtb_csv.update_date; Type: COMMENT; Schema: public; Owner: dbuser
--

COMMENT ON COLUMN public.dtb_csv.update_date IS '(DC2Type:datetimetz)';


--
-- Name: dtb_csv_id_seq; Type: SEQUENCE; Schema: public; Owner: dbuser
--

CREATE SEQUENCE public.dtb_csv_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.dtb_csv_id_seq OWNER TO dbuser;

--
-- Name: dtb_csv_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: dbuser
--

ALTER SEQUENCE public.dtb_csv_id_seq OWNED BY public.dtb_csv.id;


--
-- Name: dtb_customer; Type: TABLE; Schema: public; Owner: dbuser
--

CREATE TABLE public.dtb_customer (
    id integer NOT NULL,
    customer_status_id smallint,
    sex_id smallint,
    job_id smallint,
    country_id smallint,
    pref_id smallint,
    name01 character varying(255) NOT NULL,
    name02 character varying(255) NOT NULL,
    kana01 character varying(255) DEFAULT NULL::character varying,
    kana02 character varying(255) DEFAULT NULL::character varying,
    company_name character varying(255) DEFAULT NULL::character varying,
    postal_code character varying(8) DEFAULT NULL::character varying,
    addr01 character varying(255) DEFAULT NULL::character varying,
    addr02 character varying(255) DEFAULT NULL::character varying,
    email character varying(255) NOT NULL,
    phone_number character varying(14) DEFAULT NULL::character varying,
    birth timestamp(0) with time zone DEFAULT NULL::timestamp with time zone,
    password character varying(255) NOT NULL,
    salt character varying(255) DEFAULT NULL::character varying,
    secret_key character varying(255) NOT NULL,
    first_buy_date timestamp(0) with time zone DEFAULT NULL::timestamp with time zone,
    last_buy_date timestamp(0) with time zone DEFAULT NULL::timestamp with time zone,
    buy_times numeric(10,0) DEFAULT '0'::numeric,
    buy_total numeric(12,2) DEFAULT '0'::numeric,
    note character varying(4000) DEFAULT NULL::character varying,
    reset_key character varying(255) DEFAULT NULL::character varying,
    reset_expire timestamp(0) with time zone DEFAULT NULL::timestamp with time zone,
    point numeric(12,0) DEFAULT '0'::numeric NOT NULL,
    create_date timestamp(0) with time zone NOT NULL,
    update_date timestamp(0) with time zone NOT NULL,
    discriminator_type character varying(255) NOT NULL
);


ALTER TABLE public.dtb_customer OWNER TO dbuser;

--
-- Name: COLUMN dtb_customer.birth; Type: COMMENT; Schema: public; Owner: dbuser
--

COMMENT ON COLUMN public.dtb_customer.birth IS '(DC2Type:datetimetz)';


--
-- Name: COLUMN dtb_customer.first_buy_date; Type: COMMENT; Schema: public; Owner: dbuser
--

COMMENT ON COLUMN public.dtb_customer.first_buy_date IS '(DC2Type:datetimetz)';


--
-- Name: COLUMN dtb_customer.last_buy_date; Type: COMMENT; Schema: public; Owner: dbuser
--

COMMENT ON COLUMN public.dtb_customer.last_buy_date IS '(DC2Type:datetimetz)';


--
-- Name: COLUMN dtb_customer.reset_expire; Type: COMMENT; Schema: public; Owner: dbuser
--

COMMENT ON COLUMN public.dtb_customer.reset_expire IS '(DC2Type:datetimetz)';


--
-- Name: COLUMN dtb_customer.create_date; Type: COMMENT; Schema: public; Owner: dbuser
--

COMMENT ON COLUMN public.dtb_customer.create_date IS '(DC2Type:datetimetz)';


--
-- Name: COLUMN dtb_customer.update_date; Type: COMMENT; Schema: public; Owner: dbuser
--

COMMENT ON COLUMN public.dtb_customer.update_date IS '(DC2Type:datetimetz)';


--
-- Name: dtb_customer_address; Type: TABLE; Schema: public; Owner: dbuser
--

CREATE TABLE public.dtb_customer_address (
    id integer NOT NULL,
    customer_id integer,
    country_id smallint,
    pref_id smallint,
    name01 character varying(255) NOT NULL,
    name02 character varying(255) NOT NULL,
    kana01 character varying(255) DEFAULT NULL::character varying,
    kana02 character varying(255) DEFAULT NULL::character varying,
    company_name character varying(255) DEFAULT NULL::character varying,
    postal_code character varying(8) DEFAULT NULL::character varying,
    addr01 character varying(255) DEFAULT NULL::character varying,
    addr02 character varying(255) DEFAULT NULL::character varying,
    phone_number character varying(14) DEFAULT NULL::character varying,
    create_date timestamp(0) with time zone NOT NULL,
    update_date timestamp(0) with time zone NOT NULL,
    discriminator_type character varying(255) NOT NULL
);


ALTER TABLE public.dtb_customer_address OWNER TO dbuser;

--
-- Name: COLUMN dtb_customer_address.create_date; Type: COMMENT; Schema: public; Owner: dbuser
--

COMMENT ON COLUMN public.dtb_customer_address.create_date IS '(DC2Type:datetimetz)';


--
-- Name: COLUMN dtb_customer_address.update_date; Type: COMMENT; Schema: public; Owner: dbuser
--

COMMENT ON COLUMN public.dtb_customer_address.update_date IS '(DC2Type:datetimetz)';


--
-- Name: dtb_customer_address_id_seq; Type: SEQUENCE; Schema: public; Owner: dbuser
--

CREATE SEQUENCE public.dtb_customer_address_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.dtb_customer_address_id_seq OWNER TO dbuser;

--
-- Name: dtb_customer_address_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: dbuser
--

ALTER SEQUENCE public.dtb_customer_address_id_seq OWNED BY public.dtb_customer_address.id;


--
-- Name: dtb_customer_favorite_product; Type: TABLE; Schema: public; Owner: dbuser
--

CREATE TABLE public.dtb_customer_favorite_product (
    id integer NOT NULL,
    customer_id integer,
    product_id integer,
    create_date timestamp(0) with time zone NOT NULL,
    update_date timestamp(0) with time zone NOT NULL,
    discriminator_type character varying(255) NOT NULL
);


ALTER TABLE public.dtb_customer_favorite_product OWNER TO dbuser;

--
-- Name: COLUMN dtb_customer_favorite_product.create_date; Type: COMMENT; Schema: public; Owner: dbuser
--

COMMENT ON COLUMN public.dtb_customer_favorite_product.create_date IS '(DC2Type:datetimetz)';


--
-- Name: COLUMN dtb_customer_favorite_product.update_date; Type: COMMENT; Schema: public; Owner: dbuser
--

COMMENT ON COLUMN public.dtb_customer_favorite_product.update_date IS '(DC2Type:datetimetz)';


--
-- Name: dtb_customer_favorite_product_id_seq; Type: SEQUENCE; Schema: public; Owner: dbuser
--

CREATE SEQUENCE public.dtb_customer_favorite_product_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.dtb_customer_favorite_product_id_seq OWNER TO dbuser;

--
-- Name: dtb_customer_favorite_product_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: dbuser
--

ALTER SEQUENCE public.dtb_customer_favorite_product_id_seq OWNED BY public.dtb_customer_favorite_product.id;


--
-- Name: dtb_customer_id_seq; Type: SEQUENCE; Schema: public; Owner: dbuser
--

CREATE SEQUENCE public.dtb_customer_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.dtb_customer_id_seq OWNER TO dbuser;

--
-- Name: dtb_customer_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: dbuser
--

ALTER SEQUENCE public.dtb_customer_id_seq OWNED BY public.dtb_customer.id;


--
-- Name: dtb_delivery; Type: TABLE; Schema: public; Owner: dbuser
--

CREATE TABLE public.dtb_delivery (
    id integer NOT NULL,
    creator_id integer,
    sale_type_id smallint,
    name character varying(255) DEFAULT NULL::character varying,
    service_name character varying(255) DEFAULT NULL::character varying,
    description character varying(4000) DEFAULT NULL::character varying,
    confirm_url character varying(4000) DEFAULT NULL::character varying,
    sort_no integer,
    visible boolean DEFAULT true NOT NULL,
    create_date timestamp(0) with time zone NOT NULL,
    update_date timestamp(0) with time zone NOT NULL,
    discriminator_type character varying(255) NOT NULL
);


ALTER TABLE public.dtb_delivery OWNER TO dbuser;

--
-- Name: COLUMN dtb_delivery.create_date; Type: COMMENT; Schema: public; Owner: dbuser
--

COMMENT ON COLUMN public.dtb_delivery.create_date IS '(DC2Type:datetimetz)';


--
-- Name: COLUMN dtb_delivery.update_date; Type: COMMENT; Schema: public; Owner: dbuser
--

COMMENT ON COLUMN public.dtb_delivery.update_date IS '(DC2Type:datetimetz)';


--
-- Name: dtb_delivery_duration; Type: TABLE; Schema: public; Owner: dbuser
--

CREATE TABLE public.dtb_delivery_duration (
    id integer NOT NULL,
    name character varying(255) DEFAULT NULL::character varying,
    duration smallint DEFAULT 0 NOT NULL,
    sort_no integer NOT NULL,
    discriminator_type character varying(255) NOT NULL
);


ALTER TABLE public.dtb_delivery_duration OWNER TO dbuser;

--
-- Name: dtb_delivery_duration_id_seq; Type: SEQUENCE; Schema: public; Owner: dbuser
--

CREATE SEQUENCE public.dtb_delivery_duration_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.dtb_delivery_duration_id_seq OWNER TO dbuser;

--
-- Name: dtb_delivery_duration_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: dbuser
--

ALTER SEQUENCE public.dtb_delivery_duration_id_seq OWNED BY public.dtb_delivery_duration.id;


--
-- Name: dtb_delivery_fee; Type: TABLE; Schema: public; Owner: dbuser
--

CREATE TABLE public.dtb_delivery_fee (
    id integer NOT NULL,
    delivery_id integer,
    pref_id smallint,
    fee numeric(12,2) NOT NULL,
    discriminator_type character varying(255) NOT NULL
);


ALTER TABLE public.dtb_delivery_fee OWNER TO dbuser;

--
-- Name: dtb_delivery_fee_id_seq; Type: SEQUENCE; Schema: public; Owner: dbuser
--

CREATE SEQUENCE public.dtb_delivery_fee_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.dtb_delivery_fee_id_seq OWNER TO dbuser;

--
-- Name: dtb_delivery_fee_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: dbuser
--

ALTER SEQUENCE public.dtb_delivery_fee_id_seq OWNED BY public.dtb_delivery_fee.id;


--
-- Name: dtb_delivery_id_seq; Type: SEQUENCE; Schema: public; Owner: dbuser
--

CREATE SEQUENCE public.dtb_delivery_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.dtb_delivery_id_seq OWNER TO dbuser;

--
-- Name: dtb_delivery_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: dbuser
--

ALTER SEQUENCE public.dtb_delivery_id_seq OWNED BY public.dtb_delivery.id;


--
-- Name: dtb_delivery_time; Type: TABLE; Schema: public; Owner: dbuser
--

CREATE TABLE public.dtb_delivery_time (
    id integer NOT NULL,
    delivery_id integer,
    delivery_time character varying(255) NOT NULL,
    sort_no smallint NOT NULL,
    visible boolean DEFAULT true NOT NULL,
    create_date timestamp(0) with time zone NOT NULL,
    update_date timestamp(0) with time zone NOT NULL,
    discriminator_type character varying(255) NOT NULL
);


ALTER TABLE public.dtb_delivery_time OWNER TO dbuser;

--
-- Name: COLUMN dtb_delivery_time.create_date; Type: COMMENT; Schema: public; Owner: dbuser
--

COMMENT ON COLUMN public.dtb_delivery_time.create_date IS '(DC2Type:datetimetz)';


--
-- Name: COLUMN dtb_delivery_time.update_date; Type: COMMENT; Schema: public; Owner: dbuser
--

COMMENT ON COLUMN public.dtb_delivery_time.update_date IS '(DC2Type:datetimetz)';


--
-- Name: dtb_delivery_time_id_seq; Type: SEQUENCE; Schema: public; Owner: dbuser
--

CREATE SEQUENCE public.dtb_delivery_time_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.dtb_delivery_time_id_seq OWNER TO dbuser;

--
-- Name: dtb_delivery_time_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: dbuser
--

ALTER SEQUENCE public.dtb_delivery_time_id_seq OWNED BY public.dtb_delivery_time.id;


--
-- Name: dtb_layout; Type: TABLE; Schema: public; Owner: dbuser
--

CREATE TABLE public.dtb_layout (
    id integer NOT NULL,
    device_type_id smallint,
    layout_name character varying(255) DEFAULT NULL::character varying,
    create_date timestamp(0) with time zone NOT NULL,
    update_date timestamp(0) with time zone NOT NULL,
    discriminator_type character varying(255) NOT NULL
);


ALTER TABLE public.dtb_layout OWNER TO dbuser;

--
-- Name: COLUMN dtb_layout.create_date; Type: COMMENT; Schema: public; Owner: dbuser
--

COMMENT ON COLUMN public.dtb_layout.create_date IS '(DC2Type:datetimetz)';


--
-- Name: COLUMN dtb_layout.update_date; Type: COMMENT; Schema: public; Owner: dbuser
--

COMMENT ON COLUMN public.dtb_layout.update_date IS '(DC2Type:datetimetz)';


--
-- Name: dtb_layout_id_seq; Type: SEQUENCE; Schema: public; Owner: dbuser
--

CREATE SEQUENCE public.dtb_layout_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.dtb_layout_id_seq OWNER TO dbuser;

--
-- Name: dtb_layout_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: dbuser
--

ALTER SEQUENCE public.dtb_layout_id_seq OWNED BY public.dtb_layout.id;


--
-- Name: dtb_login_history; Type: TABLE; Schema: public; Owner: dbuser
--

CREATE TABLE public.dtb_login_history (
    id integer NOT NULL,
    login_history_status_id smallint NOT NULL,
    member_id integer,
    user_name text,
    client_ip text,
    create_date timestamp(0) with time zone NOT NULL,
    update_date timestamp(0) with time zone NOT NULL,
    discriminator_type character varying(255) NOT NULL
);


ALTER TABLE public.dtb_login_history OWNER TO dbuser;

--
-- Name: COLUMN dtb_login_history.create_date; Type: COMMENT; Schema: public; Owner: dbuser
--

COMMENT ON COLUMN public.dtb_login_history.create_date IS '(DC2Type:datetimetz)';


--
-- Name: COLUMN dtb_login_history.update_date; Type: COMMENT; Schema: public; Owner: dbuser
--

COMMENT ON COLUMN public.dtb_login_history.update_date IS '(DC2Type:datetimetz)';


--
-- Name: dtb_login_history_id_seq; Type: SEQUENCE; Schema: public; Owner: dbuser
--

CREATE SEQUENCE public.dtb_login_history_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.dtb_login_history_id_seq OWNER TO dbuser;

--
-- Name: dtb_login_history_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: dbuser
--

ALTER SEQUENCE public.dtb_login_history_id_seq OWNED BY public.dtb_login_history.id;


--
-- Name: dtb_mail_history; Type: TABLE; Schema: public; Owner: dbuser
--

CREATE TABLE public.dtb_mail_history (
    id integer NOT NULL,
    order_id integer,
    creator_id integer,
    send_date timestamp(0) with time zone DEFAULT NULL::timestamp with time zone,
    mail_subject character varying(255) DEFAULT NULL::character varying,
    mail_body text,
    mail_html_body text,
    discriminator_type character varying(255) NOT NULL
);


ALTER TABLE public.dtb_mail_history OWNER TO dbuser;

--
-- Name: COLUMN dtb_mail_history.send_date; Type: COMMENT; Schema: public; Owner: dbuser
--

COMMENT ON COLUMN public.dtb_mail_history.send_date IS '(DC2Type:datetimetz)';


--
-- Name: dtb_mail_history_id_seq; Type: SEQUENCE; Schema: public; Owner: dbuser
--

CREATE SEQUENCE public.dtb_mail_history_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.dtb_mail_history_id_seq OWNER TO dbuser;

--
-- Name: dtb_mail_history_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: dbuser
--

ALTER SEQUENCE public.dtb_mail_history_id_seq OWNED BY public.dtb_mail_history.id;


--
-- Name: dtb_mail_template; Type: TABLE; Schema: public; Owner: dbuser
--

CREATE TABLE public.dtb_mail_template (
    id integer NOT NULL,
    creator_id integer,
    name character varying(255) DEFAULT NULL::character varying,
    file_name character varying(255) DEFAULT NULL::character varying,
    mail_subject character varying(255) DEFAULT NULL::character varying,
    create_date timestamp(0) with time zone NOT NULL,
    update_date timestamp(0) with time zone NOT NULL,
    discriminator_type character varying(255) NOT NULL
);


ALTER TABLE public.dtb_mail_template OWNER TO dbuser;

--
-- Name: COLUMN dtb_mail_template.create_date; Type: COMMENT; Schema: public; Owner: dbuser
--

COMMENT ON COLUMN public.dtb_mail_template.create_date IS '(DC2Type:datetimetz)';


--
-- Name: COLUMN dtb_mail_template.update_date; Type: COMMENT; Schema: public; Owner: dbuser
--

COMMENT ON COLUMN public.dtb_mail_template.update_date IS '(DC2Type:datetimetz)';


--
-- Name: dtb_mail_template_id_seq; Type: SEQUENCE; Schema: public; Owner: dbuser
--

CREATE SEQUENCE public.dtb_mail_template_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.dtb_mail_template_id_seq OWNER TO dbuser;

--
-- Name: dtb_mail_template_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: dbuser
--

ALTER SEQUENCE public.dtb_mail_template_id_seq OWNED BY public.dtb_mail_template.id;


--
-- Name: dtb_member; Type: TABLE; Schema: public; Owner: dbuser
--

CREATE TABLE public.dtb_member (
    id integer NOT NULL,
    work_id smallint,
    authority_id smallint,
    creator_id integer,
    name character varying(255) DEFAULT NULL::character varying,
    department character varying(255) DEFAULT NULL::character varying,
    login_id character varying(255) NOT NULL,
    password character varying(255) NOT NULL,
    salt character varying(255) DEFAULT NULL::character varying,
    sort_no smallint NOT NULL,
    two_factor_auth_key character varying(255) DEFAULT NULL::character varying,
    two_factor_auth_enabled boolean DEFAULT false NOT NULL,
    create_date timestamp(0) with time zone NOT NULL,
    update_date timestamp(0) with time zone NOT NULL,
    login_date timestamp(0) with time zone DEFAULT NULL::timestamp with time zone,
    discriminator_type character varying(255) NOT NULL
);


ALTER TABLE public.dtb_member OWNER TO dbuser;

--
-- Name: COLUMN dtb_member.create_date; Type: COMMENT; Schema: public; Owner: dbuser
--

COMMENT ON COLUMN public.dtb_member.create_date IS '(DC2Type:datetimetz)';


--
-- Name: COLUMN dtb_member.update_date; Type: COMMENT; Schema: public; Owner: dbuser
--

COMMENT ON COLUMN public.dtb_member.update_date IS '(DC2Type:datetimetz)';


--
-- Name: COLUMN dtb_member.login_date; Type: COMMENT; Schema: public; Owner: dbuser
--

COMMENT ON COLUMN public.dtb_member.login_date IS '(DC2Type:datetimetz)';


--
-- Name: dtb_member_id_seq; Type: SEQUENCE; Schema: public; Owner: dbuser
--

CREATE SEQUENCE public.dtb_member_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.dtb_member_id_seq OWNER TO dbuser;

--
-- Name: dtb_member_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: dbuser
--

ALTER SEQUENCE public.dtb_member_id_seq OWNED BY public.dtb_member.id;


--
-- Name: dtb_news; Type: TABLE; Schema: public; Owner: dbuser
--

CREATE TABLE public.dtb_news (
    id integer NOT NULL,
    creator_id integer,
    publish_date timestamp(0) with time zone DEFAULT NULL::timestamp with time zone,
    title character varying(255) NOT NULL,
    description text,
    url character varying(4000) DEFAULT NULL::character varying,
    link_method boolean DEFAULT false NOT NULL,
    create_date timestamp(0) with time zone NOT NULL,
    update_date timestamp(0) with time zone NOT NULL,
    visible boolean DEFAULT true NOT NULL,
    discriminator_type character varying(255) NOT NULL
);


ALTER TABLE public.dtb_news OWNER TO dbuser;

--
-- Name: COLUMN dtb_news.publish_date; Type: COMMENT; Schema: public; Owner: dbuser
--

COMMENT ON COLUMN public.dtb_news.publish_date IS '(DC2Type:datetimetz)';


--
-- Name: COLUMN dtb_news.create_date; Type: COMMENT; Schema: public; Owner: dbuser
--

COMMENT ON COLUMN public.dtb_news.create_date IS '(DC2Type:datetimetz)';


--
-- Name: COLUMN dtb_news.update_date; Type: COMMENT; Schema: public; Owner: dbuser
--

COMMENT ON COLUMN public.dtb_news.update_date IS '(DC2Type:datetimetz)';


--
-- Name: dtb_news_id_seq; Type: SEQUENCE; Schema: public; Owner: dbuser
--

CREATE SEQUENCE public.dtb_news_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.dtb_news_id_seq OWNER TO dbuser;

--
-- Name: dtb_news_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: dbuser
--

ALTER SEQUENCE public.dtb_news_id_seq OWNED BY public.dtb_news.id;


--
-- Name: dtb_order; Type: TABLE; Schema: public; Owner: dbuser
--

CREATE TABLE public.dtb_order (
    id integer NOT NULL,
    customer_id integer,
    country_id smallint,
    pref_id smallint,
    sex_id smallint,
    job_id smallint,
    payment_id integer,
    device_type_id smallint,
    pre_order_id character varying(255) DEFAULT NULL::character varying,
    order_no character varying(255) DEFAULT NULL::character varying,
    message character varying(4000) DEFAULT NULL::character varying,
    name01 character varying(255) NOT NULL,
    name02 character varying(255) NOT NULL,
    kana01 character varying(255) DEFAULT NULL::character varying,
    kana02 character varying(255) DEFAULT NULL::character varying,
    company_name character varying(255) DEFAULT NULL::character varying,
    email character varying(255) DEFAULT NULL::character varying,
    phone_number character varying(14) DEFAULT NULL::character varying,
    postal_code character varying(8) DEFAULT NULL::character varying,
    addr01 character varying(255) DEFAULT NULL::character varying,
    addr02 character varying(255) DEFAULT NULL::character varying,
    birth timestamp(0) with time zone DEFAULT NULL::timestamp with time zone,
    subtotal numeric(12,2) DEFAULT '0'::numeric NOT NULL,
    discount numeric(12,2) DEFAULT '0'::numeric NOT NULL,
    delivery_fee_total numeric(12,2) DEFAULT '0'::numeric NOT NULL,
    charge numeric(12,2) DEFAULT '0'::numeric NOT NULL,
    tax numeric(12,2) DEFAULT '0'::numeric NOT NULL,
    total numeric(12,2) DEFAULT '0'::numeric NOT NULL,
    payment_total numeric(12,2) DEFAULT '0'::numeric NOT NULL,
    payment_method character varying(255) DEFAULT NULL::character varying,
    note character varying(4000) DEFAULT NULL::character varying,
    create_date timestamp(0) with time zone NOT NULL,
    update_date timestamp(0) with time zone NOT NULL,
    order_date timestamp(0) with time zone DEFAULT NULL::timestamp with time zone,
    payment_date timestamp(0) with time zone DEFAULT NULL::timestamp with time zone,
    currency_code character varying(255) DEFAULT NULL::character varying,
    complete_message text,
    complete_mail_message text,
    add_point numeric(12,0) DEFAULT '0'::numeric NOT NULL,
    use_point numeric(12,0) DEFAULT '0'::numeric NOT NULL,
    order_status_id smallint,
    discriminator_type character varying(255) NOT NULL
);


ALTER TABLE public.dtb_order OWNER TO dbuser;

--
-- Name: COLUMN dtb_order.birth; Type: COMMENT; Schema: public; Owner: dbuser
--

COMMENT ON COLUMN public.dtb_order.birth IS '(DC2Type:datetimetz)';


--
-- Name: COLUMN dtb_order.create_date; Type: COMMENT; Schema: public; Owner: dbuser
--

COMMENT ON COLUMN public.dtb_order.create_date IS '(DC2Type:datetimetz)';


--
-- Name: COLUMN dtb_order.update_date; Type: COMMENT; Schema: public; Owner: dbuser
--

COMMENT ON COLUMN public.dtb_order.update_date IS '(DC2Type:datetimetz)';


--
-- Name: COLUMN dtb_order.order_date; Type: COMMENT; Schema: public; Owner: dbuser
--

COMMENT ON COLUMN public.dtb_order.order_date IS '(DC2Type:datetimetz)';


--
-- Name: COLUMN dtb_order.payment_date; Type: COMMENT; Schema: public; Owner: dbuser
--

COMMENT ON COLUMN public.dtb_order.payment_date IS '(DC2Type:datetimetz)';


--
-- Name: dtb_order_id_seq; Type: SEQUENCE; Schema: public; Owner: dbuser
--

CREATE SEQUENCE public.dtb_order_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.dtb_order_id_seq OWNER TO dbuser;

--
-- Name: dtb_order_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: dbuser
--

ALTER SEQUENCE public.dtb_order_id_seq OWNED BY public.dtb_order.id;


--
-- Name: dtb_order_item; Type: TABLE; Schema: public; Owner: dbuser
--

CREATE TABLE public.dtb_order_item (
    id integer NOT NULL,
    order_id integer,
    product_id integer,
    product_class_id integer,
    shipping_id integer,
    rounding_type_id smallint,
    tax_type_id smallint,
    tax_display_type_id smallint,
    order_item_type_id smallint,
    product_name character varying(255) NOT NULL,
    product_code character varying(255) DEFAULT NULL::character varying,
    class_name1 character varying(255) DEFAULT NULL::character varying,
    class_name2 character varying(255) DEFAULT NULL::character varying,
    class_category_name1 character varying(255) DEFAULT NULL::character varying,
    class_category_name2 character varying(255) DEFAULT NULL::character varying,
    price numeric(12,2) DEFAULT '0'::numeric NOT NULL,
    quantity numeric(10,0) DEFAULT '0'::numeric NOT NULL,
    tax numeric(10,0) DEFAULT '0'::numeric NOT NULL,
    tax_rate numeric(10,0) DEFAULT '0'::numeric NOT NULL,
    tax_adjust numeric(10,0) DEFAULT '0'::numeric NOT NULL,
    tax_rule_id smallint,
    currency_code character varying(255) DEFAULT NULL::character varying,
    processor_name character varying(255) DEFAULT NULL::character varying,
    point_rate numeric(10,0) DEFAULT NULL::numeric,
    discriminator_type character varying(255) NOT NULL
);


ALTER TABLE public.dtb_order_item OWNER TO dbuser;

--
-- Name: dtb_order_item_id_seq; Type: SEQUENCE; Schema: public; Owner: dbuser
--

CREATE SEQUENCE public.dtb_order_item_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.dtb_order_item_id_seq OWNER TO dbuser;

--
-- Name: dtb_order_item_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: dbuser
--

ALTER SEQUENCE public.dtb_order_item_id_seq OWNED BY public.dtb_order_item.id;


--
-- Name: dtb_order_pdf; Type: TABLE; Schema: public; Owner: dbuser
--

CREATE TABLE public.dtb_order_pdf (
    member_id integer NOT NULL,
    title character varying(255) DEFAULT NULL::character varying,
    message1 character varying(255) DEFAULT NULL::character varying,
    message2 character varying(255) DEFAULT NULL::character varying,
    message3 character varying(255) DEFAULT NULL::character varying,
    note1 character varying(255) DEFAULT NULL::character varying,
    note2 character varying(255) DEFAULT NULL::character varying,
    note3 character varying(255) DEFAULT NULL::character varying,
    create_date timestamp(0) with time zone NOT NULL,
    update_date timestamp(0) with time zone NOT NULL,
    visible boolean DEFAULT true NOT NULL,
    discriminator_type character varying(255) NOT NULL
);


ALTER TABLE public.dtb_order_pdf OWNER TO dbuser;

--
-- Name: COLUMN dtb_order_pdf.create_date; Type: COMMENT; Schema: public; Owner: dbuser
--

COMMENT ON COLUMN public.dtb_order_pdf.create_date IS '(DC2Type:datetimetz)';


--
-- Name: COLUMN dtb_order_pdf.update_date; Type: COMMENT; Schema: public; Owner: dbuser
--

COMMENT ON COLUMN public.dtb_order_pdf.update_date IS '(DC2Type:datetimetz)';


--
-- Name: dtb_page; Type: TABLE; Schema: public; Owner: dbuser
--

CREATE TABLE public.dtb_page (
    id integer NOT NULL,
    master_page_id integer,
    page_name character varying(255) DEFAULT NULL::character varying,
    url character varying(255) NOT NULL,
    file_name character varying(255) DEFAULT NULL::character varying,
    edit_type smallint DEFAULT 1 NOT NULL,
    author character varying(255) DEFAULT NULL::character varying,
    description character varying(255) DEFAULT NULL::character varying,
    keyword character varying(255) DEFAULT NULL::character varying,
    create_date timestamp(0) with time zone NOT NULL,
    update_date timestamp(0) with time zone NOT NULL,
    meta_robots character varying(255) DEFAULT NULL::character varying,
    meta_tags character varying(4000) DEFAULT NULL::character varying,
    discriminator_type character varying(255) NOT NULL
);


ALTER TABLE public.dtb_page OWNER TO dbuser;

--
-- Name: COLUMN dtb_page.create_date; Type: COMMENT; Schema: public; Owner: dbuser
--

COMMENT ON COLUMN public.dtb_page.create_date IS '(DC2Type:datetimetz)';


--
-- Name: COLUMN dtb_page.update_date; Type: COMMENT; Schema: public; Owner: dbuser
--

COMMENT ON COLUMN public.dtb_page.update_date IS '(DC2Type:datetimetz)';


--
-- Name: dtb_page_id_seq; Type: SEQUENCE; Schema: public; Owner: dbuser
--

CREATE SEQUENCE public.dtb_page_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.dtb_page_id_seq OWNER TO dbuser;

--
-- Name: dtb_page_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: dbuser
--

ALTER SEQUENCE public.dtb_page_id_seq OWNED BY public.dtb_page.id;


--
-- Name: dtb_page_layout; Type: TABLE; Schema: public; Owner: dbuser
--

CREATE TABLE public.dtb_page_layout (
    page_id integer NOT NULL,
    layout_id integer NOT NULL,
    sort_no smallint NOT NULL,
    discriminator_type character varying(255) NOT NULL
);


ALTER TABLE public.dtb_page_layout OWNER TO dbuser;

--
-- Name: dtb_payment; Type: TABLE; Schema: public; Owner: dbuser
--

CREATE TABLE public.dtb_payment (
    id integer NOT NULL,
    creator_id integer,
    payment_method character varying(255) DEFAULT NULL::character varying,
    charge numeric(12,2) DEFAULT '0'::numeric,
    rule_max numeric(12,2) DEFAULT NULL::numeric,
    sort_no smallint,
    fixed boolean DEFAULT true NOT NULL,
    payment_image character varying(255) DEFAULT NULL::character varying,
    rule_min numeric(12,2) DEFAULT NULL::numeric,
    method_class character varying(255) DEFAULT NULL::character varying,
    visible boolean DEFAULT true NOT NULL,
    create_date timestamp(0) with time zone NOT NULL,
    update_date timestamp(0) with time zone NOT NULL,
    discriminator_type character varying(255) NOT NULL
);


ALTER TABLE public.dtb_payment OWNER TO dbuser;

--
-- Name: COLUMN dtb_payment.create_date; Type: COMMENT; Schema: public; Owner: dbuser
--

COMMENT ON COLUMN public.dtb_payment.create_date IS '(DC2Type:datetimetz)';


--
-- Name: COLUMN dtb_payment.update_date; Type: COMMENT; Schema: public; Owner: dbuser
--

COMMENT ON COLUMN public.dtb_payment.update_date IS '(DC2Type:datetimetz)';


--
-- Name: dtb_payment_id_seq; Type: SEQUENCE; Schema: public; Owner: dbuser
--

CREATE SEQUENCE public.dtb_payment_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.dtb_payment_id_seq OWNER TO dbuser;

--
-- Name: dtb_payment_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: dbuser
--

ALTER SEQUENCE public.dtb_payment_id_seq OWNED BY public.dtb_payment.id;


--
-- Name: dtb_payment_option; Type: TABLE; Schema: public; Owner: dbuser
--

CREATE TABLE public.dtb_payment_option (
    delivery_id integer NOT NULL,
    payment_id integer NOT NULL,
    discriminator_type character varying(255) NOT NULL
);


ALTER TABLE public.dtb_payment_option OWNER TO dbuser;

--
-- Name: dtb_plugin; Type: TABLE; Schema: public; Owner: dbuser
--

CREATE TABLE public.dtb_plugin (
    id integer NOT NULL,
    name character varying(255) NOT NULL,
    code character varying(255) NOT NULL,
    enabled boolean DEFAULT false NOT NULL,
    version character varying(255) NOT NULL,
    source character varying(255) NOT NULL,
    initialized boolean DEFAULT false NOT NULL,
    create_date timestamp(0) with time zone NOT NULL,
    update_date timestamp(0) with time zone NOT NULL,
    discriminator_type character varying(255) NOT NULL
);


ALTER TABLE public.dtb_plugin OWNER TO dbuser;

--
-- Name: COLUMN dtb_plugin.create_date; Type: COMMENT; Schema: public; Owner: dbuser
--

COMMENT ON COLUMN public.dtb_plugin.create_date IS '(DC2Type:datetimetz)';


--
-- Name: COLUMN dtb_plugin.update_date; Type: COMMENT; Schema: public; Owner: dbuser
--

COMMENT ON COLUMN public.dtb_plugin.update_date IS '(DC2Type:datetimetz)';


--
-- Name: dtb_plugin_id_seq; Type: SEQUENCE; Schema: public; Owner: dbuser
--

CREATE SEQUENCE public.dtb_plugin_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.dtb_plugin_id_seq OWNER TO dbuser;

--
-- Name: dtb_plugin_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: dbuser
--

ALTER SEQUENCE public.dtb_plugin_id_seq OWNED BY public.dtb_plugin.id;


--
-- Name: dtb_product; Type: TABLE; Schema: public; Owner: dbuser
--

CREATE TABLE public.dtb_product (
    id integer NOT NULL,
    creator_id integer,
    product_status_id smallint,
    name character varying(255) NOT NULL,
    note character varying(4000) DEFAULT NULL::character varying,
    description_list character varying(4000) DEFAULT NULL::character varying,
    description_detail character varying(4000) DEFAULT NULL::character varying,
    search_word character varying(4000) DEFAULT NULL::character varying,
    free_area text,
    create_date timestamp(0) with time zone NOT NULL,
    update_date timestamp(0) with time zone NOT NULL,
    discriminator_type character varying(255) NOT NULL
);


ALTER TABLE public.dtb_product OWNER TO dbuser;

--
-- Name: COLUMN dtb_product.create_date; Type: COMMENT; Schema: public; Owner: dbuser
--

COMMENT ON COLUMN public.dtb_product.create_date IS '(DC2Type:datetimetz)';


--
-- Name: COLUMN dtb_product.update_date; Type: COMMENT; Schema: public; Owner: dbuser
--

COMMENT ON COLUMN public.dtb_product.update_date IS '(DC2Type:datetimetz)';


--
-- Name: dtb_product_category; Type: TABLE; Schema: public; Owner: dbuser
--

CREATE TABLE public.dtb_product_category (
    product_id integer NOT NULL,
    category_id integer NOT NULL,
    discriminator_type character varying(255) NOT NULL
);


ALTER TABLE public.dtb_product_category OWNER TO dbuser;

--
-- Name: dtb_product_class; Type: TABLE; Schema: public; Owner: dbuser
--

CREATE TABLE public.dtb_product_class (
    id integer NOT NULL,
    product_id integer,
    sale_type_id smallint,
    class_category_id1 integer,
    class_category_id2 integer,
    delivery_duration_id integer,
    creator_id integer,
    product_code character varying(255) DEFAULT NULL::character varying,
    stock numeric(10,0) DEFAULT NULL::numeric,
    stock_unlimited boolean DEFAULT false NOT NULL,
    sale_limit numeric(10,0) DEFAULT NULL::numeric,
    price01 numeric(12,2) DEFAULT NULL::numeric,
    price02 numeric(12,2) NOT NULL,
    delivery_fee numeric(12,2) DEFAULT NULL::numeric,
    visible boolean DEFAULT true NOT NULL,
    create_date timestamp(0) with time zone NOT NULL,
    update_date timestamp(0) with time zone NOT NULL,
    currency_code character varying(255) DEFAULT NULL::character varying,
    point_rate numeric(10,0) DEFAULT NULL::numeric,
    discriminator_type character varying(255) NOT NULL
);


ALTER TABLE public.dtb_product_class OWNER TO dbuser;

--
-- Name: COLUMN dtb_product_class.create_date; Type: COMMENT; Schema: public; Owner: dbuser
--

COMMENT ON COLUMN public.dtb_product_class.create_date IS '(DC2Type:datetimetz)';


--
-- Name: COLUMN dtb_product_class.update_date; Type: COMMENT; Schema: public; Owner: dbuser
--

COMMENT ON COLUMN public.dtb_product_class.update_date IS '(DC2Type:datetimetz)';


--
-- Name: dtb_product_class_id_seq; Type: SEQUENCE; Schema: public; Owner: dbuser
--

CREATE SEQUENCE public.dtb_product_class_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.dtb_product_class_id_seq OWNER TO dbuser;

--
-- Name: dtb_product_class_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: dbuser
--

ALTER SEQUENCE public.dtb_product_class_id_seq OWNED BY public.dtb_product_class.id;


--
-- Name: dtb_product_id_seq; Type: SEQUENCE; Schema: public; Owner: dbuser
--

CREATE SEQUENCE public.dtb_product_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.dtb_product_id_seq OWNER TO dbuser;

--
-- Name: dtb_product_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: dbuser
--

ALTER SEQUENCE public.dtb_product_id_seq OWNED BY public.dtb_product.id;


--
-- Name: dtb_product_image; Type: TABLE; Schema: public; Owner: dbuser
--

CREATE TABLE public.dtb_product_image (
    id integer NOT NULL,
    product_id integer,
    creator_id integer,
    file_name character varying(255) NOT NULL,
    sort_no smallint NOT NULL,
    create_date timestamp(0) with time zone NOT NULL,
    discriminator_type character varying(255) NOT NULL
);


ALTER TABLE public.dtb_product_image OWNER TO dbuser;

--
-- Name: COLUMN dtb_product_image.create_date; Type: COMMENT; Schema: public; Owner: dbuser
--

COMMENT ON COLUMN public.dtb_product_image.create_date IS '(DC2Type:datetimetz)';


--
-- Name: dtb_product_image_id_seq; Type: SEQUENCE; Schema: public; Owner: dbuser
--

CREATE SEQUENCE public.dtb_product_image_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.dtb_product_image_id_seq OWNER TO dbuser;

--
-- Name: dtb_product_image_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: dbuser
--

ALTER SEQUENCE public.dtb_product_image_id_seq OWNED BY public.dtb_product_image.id;


--
-- Name: dtb_product_stock; Type: TABLE; Schema: public; Owner: dbuser
--

CREATE TABLE public.dtb_product_stock (
    id integer NOT NULL,
    product_class_id integer,
    creator_id integer,
    stock numeric(10,0) DEFAULT NULL::numeric,
    create_date timestamp(0) with time zone NOT NULL,
    update_date timestamp(0) with time zone NOT NULL,
    discriminator_type character varying(255) NOT NULL
);


ALTER TABLE public.dtb_product_stock OWNER TO dbuser;

--
-- Name: COLUMN dtb_product_stock.create_date; Type: COMMENT; Schema: public; Owner: dbuser
--

COMMENT ON COLUMN public.dtb_product_stock.create_date IS '(DC2Type:datetimetz)';


--
-- Name: COLUMN dtb_product_stock.update_date; Type: COMMENT; Schema: public; Owner: dbuser
--

COMMENT ON COLUMN public.dtb_product_stock.update_date IS '(DC2Type:datetimetz)';


--
-- Name: dtb_product_stock_id_seq; Type: SEQUENCE; Schema: public; Owner: dbuser
--

CREATE SEQUENCE public.dtb_product_stock_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.dtb_product_stock_id_seq OWNER TO dbuser;

--
-- Name: dtb_product_stock_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: dbuser
--

ALTER SEQUENCE public.dtb_product_stock_id_seq OWNED BY public.dtb_product_stock.id;


--
-- Name: dtb_product_tag; Type: TABLE; Schema: public; Owner: dbuser
--

CREATE TABLE public.dtb_product_tag (
    id integer NOT NULL,
    product_id integer,
    tag_id integer,
    creator_id integer,
    create_date timestamp(0) with time zone NOT NULL,
    discriminator_type character varying(255) NOT NULL
);


ALTER TABLE public.dtb_product_tag OWNER TO dbuser;

--
-- Name: COLUMN dtb_product_tag.create_date; Type: COMMENT; Schema: public; Owner: dbuser
--

COMMENT ON COLUMN public.dtb_product_tag.create_date IS '(DC2Type:datetimetz)';


--
-- Name: dtb_product_tag_id_seq; Type: SEQUENCE; Schema: public; Owner: dbuser
--

CREATE SEQUENCE public.dtb_product_tag_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.dtb_product_tag_id_seq OWNER TO dbuser;

--
-- Name: dtb_product_tag_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: dbuser
--

ALTER SEQUENCE public.dtb_product_tag_id_seq OWNED BY public.dtb_product_tag.id;


--
-- Name: dtb_shipping; Type: TABLE; Schema: public; Owner: dbuser
--

CREATE TABLE public.dtb_shipping (
    id integer NOT NULL,
    order_id integer,
    country_id smallint,
    pref_id smallint,
    delivery_id integer,
    creator_id integer,
    name01 character varying(255) NOT NULL,
    name02 character varying(255) NOT NULL,
    kana01 character varying(255) DEFAULT NULL::character varying,
    kana02 character varying(255) DEFAULT NULL::character varying,
    company_name character varying(255) DEFAULT NULL::character varying,
    phone_number character varying(14) DEFAULT NULL::character varying,
    postal_code character varying(8) DEFAULT NULL::character varying,
    addr01 character varying(255) DEFAULT NULL::character varying,
    addr02 character varying(255) DEFAULT NULL::character varying,
    delivery_name character varying(255) DEFAULT NULL::character varying,
    time_id integer,
    delivery_time character varying(255) DEFAULT NULL::character varying,
    delivery_date timestamp(0) with time zone DEFAULT NULL::timestamp with time zone,
    shipping_date timestamp(0) with time zone DEFAULT NULL::timestamp with time zone,
    tracking_number character varying(255) DEFAULT NULL::character varying,
    note character varying(4000) DEFAULT NULL::character varying,
    sort_no smallint,
    create_date timestamp(0) with time zone NOT NULL,
    update_date timestamp(0) with time zone NOT NULL,
    mail_send_date timestamp(0) with time zone DEFAULT NULL::timestamp with time zone,
    discriminator_type character varying(255) NOT NULL
);


ALTER TABLE public.dtb_shipping OWNER TO dbuser;

--
-- Name: COLUMN dtb_shipping.delivery_date; Type: COMMENT; Schema: public; Owner: dbuser
--

COMMENT ON COLUMN public.dtb_shipping.delivery_date IS '(DC2Type:datetimetz)';


--
-- Name: COLUMN dtb_shipping.shipping_date; Type: COMMENT; Schema: public; Owner: dbuser
--

COMMENT ON COLUMN public.dtb_shipping.shipping_date IS '(DC2Type:datetimetz)';


--
-- Name: COLUMN dtb_shipping.create_date; Type: COMMENT; Schema: public; Owner: dbuser
--

COMMENT ON COLUMN public.dtb_shipping.create_date IS '(DC2Type:datetimetz)';


--
-- Name: COLUMN dtb_shipping.update_date; Type: COMMENT; Schema: public; Owner: dbuser
--

COMMENT ON COLUMN public.dtb_shipping.update_date IS '(DC2Type:datetimetz)';


--
-- Name: COLUMN dtb_shipping.mail_send_date; Type: COMMENT; Schema: public; Owner: dbuser
--

COMMENT ON COLUMN public.dtb_shipping.mail_send_date IS '(DC2Type:datetimetz)';


--
-- Name: dtb_shipping_id_seq; Type: SEQUENCE; Schema: public; Owner: dbuser
--

CREATE SEQUENCE public.dtb_shipping_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.dtb_shipping_id_seq OWNER TO dbuser;

--
-- Name: dtb_shipping_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: dbuser
--

ALTER SEQUENCE public.dtb_shipping_id_seq OWNED BY public.dtb_shipping.id;


--
-- Name: dtb_tag; Type: TABLE; Schema: public; Owner: dbuser
--

CREATE TABLE public.dtb_tag (
    id integer NOT NULL,
    name character varying(255) NOT NULL,
    sort_no smallint NOT NULL,
    discriminator_type character varying(255) NOT NULL
);


ALTER TABLE public.dtb_tag OWNER TO dbuser;

--
-- Name: dtb_tag_id_seq; Type: SEQUENCE; Schema: public; Owner: dbuser
--

CREATE SEQUENCE public.dtb_tag_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.dtb_tag_id_seq OWNER TO dbuser;

--
-- Name: dtb_tag_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: dbuser
--

ALTER SEQUENCE public.dtb_tag_id_seq OWNED BY public.dtb_tag.id;


--
-- Name: dtb_tax_rule; Type: TABLE; Schema: public; Owner: dbuser
--

CREATE TABLE public.dtb_tax_rule (
    id integer NOT NULL,
    product_class_id integer,
    creator_id integer,
    country_id smallint,
    pref_id smallint,
    product_id integer,
    rounding_type_id smallint,
    tax_rate numeric(10,0) DEFAULT '0'::numeric NOT NULL,
    tax_adjust numeric(10,0) DEFAULT '0'::numeric NOT NULL,
    apply_date timestamp(0) with time zone NOT NULL,
    create_date timestamp(0) with time zone NOT NULL,
    update_date timestamp(0) with time zone NOT NULL,
    discriminator_type character varying(255) NOT NULL
);


ALTER TABLE public.dtb_tax_rule OWNER TO dbuser;

--
-- Name: COLUMN dtb_tax_rule.apply_date; Type: COMMENT; Schema: public; Owner: dbuser
--

COMMENT ON COLUMN public.dtb_tax_rule.apply_date IS '(DC2Type:datetimetz)';


--
-- Name: COLUMN dtb_tax_rule.create_date; Type: COMMENT; Schema: public; Owner: dbuser
--

COMMENT ON COLUMN public.dtb_tax_rule.create_date IS '(DC2Type:datetimetz)';


--
-- Name: COLUMN dtb_tax_rule.update_date; Type: COMMENT; Schema: public; Owner: dbuser
--

COMMENT ON COLUMN public.dtb_tax_rule.update_date IS '(DC2Type:datetimetz)';


--
-- Name: dtb_tax_rule_id_seq; Type: SEQUENCE; Schema: public; Owner: dbuser
--

CREATE SEQUENCE public.dtb_tax_rule_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.dtb_tax_rule_id_seq OWNER TO dbuser;

--
-- Name: dtb_tax_rule_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: dbuser
--

ALTER SEQUENCE public.dtb_tax_rule_id_seq OWNED BY public.dtb_tax_rule.id;


--
-- Name: dtb_template; Type: TABLE; Schema: public; Owner: dbuser
--

CREATE TABLE public.dtb_template (
    id integer NOT NULL,
    device_type_id smallint,
    template_code character varying(255) NOT NULL,
    template_name character varying(255) NOT NULL,
    create_date timestamp(0) with time zone NOT NULL,
    update_date timestamp(0) with time zone NOT NULL,
    discriminator_type character varying(255) NOT NULL
);


ALTER TABLE public.dtb_template OWNER TO dbuser;

--
-- Name: COLUMN dtb_template.create_date; Type: COMMENT; Schema: public; Owner: dbuser
--

COMMENT ON COLUMN public.dtb_template.create_date IS '(DC2Type:datetimetz)';


--
-- Name: COLUMN dtb_template.update_date; Type: COMMENT; Schema: public; Owner: dbuser
--

COMMENT ON COLUMN public.dtb_template.update_date IS '(DC2Type:datetimetz)';


--
-- Name: dtb_template_id_seq; Type: SEQUENCE; Schema: public; Owner: dbuser
--

CREATE SEQUENCE public.dtb_template_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.dtb_template_id_seq OWNER TO dbuser;

--
-- Name: dtb_template_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: dbuser
--

ALTER SEQUENCE public.dtb_template_id_seq OWNED BY public.dtb_template.id;


--
-- Name: mtb_authority; Type: TABLE; Schema: public; Owner: dbuser
--

CREATE TABLE public.mtb_authority (
    id smallint NOT NULL,
    name character varying(255) NOT NULL,
    sort_no smallint NOT NULL,
    discriminator_type character varying(255) NOT NULL
);


ALTER TABLE public.mtb_authority OWNER TO dbuser;

--
-- Name: mtb_country; Type: TABLE; Schema: public; Owner: dbuser
--

CREATE TABLE public.mtb_country (
    id smallint NOT NULL,
    name character varying(255) NOT NULL,
    sort_no smallint NOT NULL,
    discriminator_type character varying(255) NOT NULL
);


ALTER TABLE public.mtb_country OWNER TO dbuser;

--
-- Name: mtb_csv_type; Type: TABLE; Schema: public; Owner: dbuser
--

CREATE TABLE public.mtb_csv_type (
    id smallint NOT NULL,
    name character varying(255) NOT NULL,
    sort_no smallint NOT NULL,
    discriminator_type character varying(255) NOT NULL
);


ALTER TABLE public.mtb_csv_type OWNER TO dbuser;

--
-- Name: mtb_customer_order_status; Type: TABLE; Schema: public; Owner: dbuser
--

CREATE TABLE public.mtb_customer_order_status (
    id smallint NOT NULL,
    name character varying(255) NOT NULL,
    sort_no smallint NOT NULL,
    discriminator_type character varying(255) NOT NULL
);


ALTER TABLE public.mtb_customer_order_status OWNER TO dbuser;

--
-- Name: mtb_customer_status; Type: TABLE; Schema: public; Owner: dbuser
--

CREATE TABLE public.mtb_customer_status (
    id smallint NOT NULL,
    name character varying(255) NOT NULL,
    sort_no smallint NOT NULL,
    discriminator_type character varying(255) NOT NULL
);


ALTER TABLE public.mtb_customer_status OWNER TO dbuser;

--
-- Name: mtb_device_type; Type: TABLE; Schema: public; Owner: dbuser
--

CREATE TABLE public.mtb_device_type (
    id smallint NOT NULL,
    name character varying(255) NOT NULL,
    sort_no smallint NOT NULL,
    discriminator_type character varying(255) NOT NULL
);


ALTER TABLE public.mtb_device_type OWNER TO dbuser;

--
-- Name: mtb_job; Type: TABLE; Schema: public; Owner: dbuser
--

CREATE TABLE public.mtb_job (
    id smallint NOT NULL,
    name character varying(255) NOT NULL,
    sort_no smallint NOT NULL,
    discriminator_type character varying(255) NOT NULL
);


ALTER TABLE public.mtb_job OWNER TO dbuser;

--
-- Name: mtb_login_history_status; Type: TABLE; Schema: public; Owner: dbuser
--

CREATE TABLE public.mtb_login_history_status (
    id smallint NOT NULL,
    name character varying(255) NOT NULL,
    sort_no smallint NOT NULL,
    discriminator_type character varying(255) NOT NULL
);


ALTER TABLE public.mtb_login_history_status OWNER TO dbuser;

--
-- Name: mtb_order_item_type; Type: TABLE; Schema: public; Owner: dbuser
--

CREATE TABLE public.mtb_order_item_type (
    id smallint NOT NULL,
    name character varying(255) NOT NULL,
    sort_no smallint NOT NULL,
    discriminator_type character varying(255) NOT NULL
);


ALTER TABLE public.mtb_order_item_type OWNER TO dbuser;

--
-- Name: mtb_order_status; Type: TABLE; Schema: public; Owner: dbuser
--

CREATE TABLE public.mtb_order_status (
    id smallint NOT NULL,
    display_order_count boolean DEFAULT false NOT NULL,
    name character varying(255) NOT NULL,
    sort_no smallint NOT NULL,
    discriminator_type character varying(255) NOT NULL
);


ALTER TABLE public.mtb_order_status OWNER TO dbuser;

--
-- Name: mtb_order_status_color; Type: TABLE; Schema: public; Owner: dbuser
--

CREATE TABLE public.mtb_order_status_color (
    id smallint NOT NULL,
    name character varying(255) NOT NULL,
    sort_no smallint NOT NULL,
    discriminator_type character varying(255) NOT NULL
);


ALTER TABLE public.mtb_order_status_color OWNER TO dbuser;

--
-- Name: mtb_page_max; Type: TABLE; Schema: public; Owner: dbuser
--

CREATE TABLE public.mtb_page_max (
    id smallint NOT NULL,
    name character varying(255) NOT NULL,
    sort_no smallint NOT NULL,
    discriminator_type character varying(255) NOT NULL
);


ALTER TABLE public.mtb_page_max OWNER TO dbuser;

--
-- Name: mtb_pref; Type: TABLE; Schema: public; Owner: dbuser
--

CREATE TABLE public.mtb_pref (
    id smallint NOT NULL,
    name character varying(255) NOT NULL,
    sort_no smallint NOT NULL,
    discriminator_type character varying(255) NOT NULL
);


ALTER TABLE public.mtb_pref OWNER TO dbuser;

--
-- Name: mtb_product_list_max; Type: TABLE; Schema: public; Owner: dbuser
--

CREATE TABLE public.mtb_product_list_max (
    id smallint NOT NULL,
    name character varying(255) NOT NULL,
    sort_no smallint NOT NULL,
    discriminator_type character varying(255) NOT NULL
);


ALTER TABLE public.mtb_product_list_max OWNER TO dbuser;

--
-- Name: mtb_product_list_order_by; Type: TABLE; Schema: public; Owner: dbuser
--

CREATE TABLE public.mtb_product_list_order_by (
    id smallint NOT NULL,
    name character varying(255) NOT NULL,
    sort_no smallint NOT NULL,
    discriminator_type character varying(255) NOT NULL
);


ALTER TABLE public.mtb_product_list_order_by OWNER TO dbuser;

--
-- Name: mtb_product_status; Type: TABLE; Schema: public; Owner: dbuser
--

CREATE TABLE public.mtb_product_status (
    id smallint NOT NULL,
    name character varying(255) NOT NULL,
    sort_no smallint NOT NULL,
    discriminator_type character varying(255) NOT NULL
);


ALTER TABLE public.mtb_product_status OWNER TO dbuser;

--
-- Name: mtb_rounding_type; Type: TABLE; Schema: public; Owner: dbuser
--

CREATE TABLE public.mtb_rounding_type (
    id smallint NOT NULL,
    name character varying(255) NOT NULL,
    sort_no smallint NOT NULL,
    discriminator_type character varying(255) NOT NULL
);


ALTER TABLE public.mtb_rounding_type OWNER TO dbuser;

--
-- Name: mtb_sale_type; Type: TABLE; Schema: public; Owner: dbuser
--

CREATE TABLE public.mtb_sale_type (
    id smallint NOT NULL,
    name character varying(255) NOT NULL,
    sort_no smallint NOT NULL,
    discriminator_type character varying(255) NOT NULL
);


ALTER TABLE public.mtb_sale_type OWNER TO dbuser;

--
-- Name: mtb_sex; Type: TABLE; Schema: public; Owner: dbuser
--

CREATE TABLE public.mtb_sex (
    id smallint NOT NULL,
    name character varying(255) NOT NULL,
    sort_no smallint NOT NULL,
    discriminator_type character varying(255) NOT NULL
);


ALTER TABLE public.mtb_sex OWNER TO dbuser;

--
-- Name: mtb_tax_display_type; Type: TABLE; Schema: public; Owner: dbuser
--

CREATE TABLE public.mtb_tax_display_type (
    id smallint NOT NULL,
    name character varying(255) NOT NULL,
    sort_no smallint NOT NULL,
    discriminator_type character varying(255) NOT NULL
);


ALTER TABLE public.mtb_tax_display_type OWNER TO dbuser;

--
-- Name: mtb_tax_type; Type: TABLE; Schema: public; Owner: dbuser
--

CREATE TABLE public.mtb_tax_type (
    id smallint NOT NULL,
    name character varying(255) NOT NULL,
    sort_no smallint NOT NULL,
    discriminator_type character varying(255) NOT NULL
);


ALTER TABLE public.mtb_tax_type OWNER TO dbuser;

--
-- Name: mtb_work; Type: TABLE; Schema: public; Owner: dbuser
--

CREATE TABLE public.mtb_work (
    id smallint NOT NULL,
    name character varying(255) NOT NULL,
    sort_no smallint NOT NULL,
    discriminator_type character varying(255) NOT NULL
);


ALTER TABLE public.mtb_work OWNER TO dbuser;

--
-- Name: dtb_authority_role id; Type: DEFAULT; Schema: public; Owner: dbuser
--

ALTER TABLE ONLY public.dtb_authority_role ALTER COLUMN id SET DEFAULT nextval('public.dtb_authority_role_id_seq'::regclass);


--
-- Name: dtb_base_info id; Type: DEFAULT; Schema: public; Owner: dbuser
--

ALTER TABLE ONLY public.dtb_base_info ALTER COLUMN id SET DEFAULT nextval('public.dtb_base_info_id_seq'::regclass);


--
-- Name: dtb_block id; Type: DEFAULT; Schema: public; Owner: dbuser
--

ALTER TABLE ONLY public.dtb_block ALTER COLUMN id SET DEFAULT nextval('public.dtb_block_id_seq'::regclass);


--
-- Name: dtb_calendar id; Type: DEFAULT; Schema: public; Owner: dbuser
--

ALTER TABLE ONLY public.dtb_calendar ALTER COLUMN id SET DEFAULT nextval('public.dtb_calendar_id_seq'::regclass);


--
-- Name: dtb_cart id; Type: DEFAULT; Schema: public; Owner: dbuser
--

ALTER TABLE ONLY public.dtb_cart ALTER COLUMN id SET DEFAULT nextval('public.dtb_cart_id_seq'::regclass);


--
-- Name: dtb_cart_item id; Type: DEFAULT; Schema: public; Owner: dbuser
--

ALTER TABLE ONLY public.dtb_cart_item ALTER COLUMN id SET DEFAULT nextval('public.dtb_cart_item_id_seq'::regclass);


--
-- Name: dtb_category id; Type: DEFAULT; Schema: public; Owner: dbuser
--

ALTER TABLE ONLY public.dtb_category ALTER COLUMN id SET DEFAULT nextval('public.dtb_category_id_seq'::regclass);


--
-- Name: dtb_class_category id; Type: DEFAULT; Schema: public; Owner: dbuser
--

ALTER TABLE ONLY public.dtb_class_category ALTER COLUMN id SET DEFAULT nextval('public.dtb_class_category_id_seq'::regclass);


--
-- Name: dtb_class_name id; Type: DEFAULT; Schema: public; Owner: dbuser
--

ALTER TABLE ONLY public.dtb_class_name ALTER COLUMN id SET DEFAULT nextval('public.dtb_class_name_id_seq'::regclass);


--
-- Name: dtb_csv id; Type: DEFAULT; Schema: public; Owner: dbuser
--

ALTER TABLE ONLY public.dtb_csv ALTER COLUMN id SET DEFAULT nextval('public.dtb_csv_id_seq'::regclass);


--
-- Name: dtb_customer id; Type: DEFAULT; Schema: public; Owner: dbuser
--

ALTER TABLE ONLY public.dtb_customer ALTER COLUMN id SET DEFAULT nextval('public.dtb_customer_id_seq'::regclass);


--
-- Name: dtb_customer_address id; Type: DEFAULT; Schema: public; Owner: dbuser
--

ALTER TABLE ONLY public.dtb_customer_address ALTER COLUMN id SET DEFAULT nextval('public.dtb_customer_address_id_seq'::regclass);


--
-- Name: dtb_customer_favorite_product id; Type: DEFAULT; Schema: public; Owner: dbuser
--

ALTER TABLE ONLY public.dtb_customer_favorite_product ALTER COLUMN id SET DEFAULT nextval('public.dtb_customer_favorite_product_id_seq'::regclass);


--
-- Name: dtb_delivery id; Type: DEFAULT; Schema: public; Owner: dbuser
--

ALTER TABLE ONLY public.dtb_delivery ALTER COLUMN id SET DEFAULT nextval('public.dtb_delivery_id_seq'::regclass);


--
-- Name: dtb_delivery_duration id; Type: DEFAULT; Schema: public; Owner: dbuser
--

ALTER TABLE ONLY public.dtb_delivery_duration ALTER COLUMN id SET DEFAULT nextval('public.dtb_delivery_duration_id_seq'::regclass);


--
-- Name: dtb_delivery_fee id; Type: DEFAULT; Schema: public; Owner: dbuser
--

ALTER TABLE ONLY public.dtb_delivery_fee ALTER COLUMN id SET DEFAULT nextval('public.dtb_delivery_fee_id_seq'::regclass);


--
-- Name: dtb_delivery_time id; Type: DEFAULT; Schema: public; Owner: dbuser
--

ALTER TABLE ONLY public.dtb_delivery_time ALTER COLUMN id SET DEFAULT nextval('public.dtb_delivery_time_id_seq'::regclass);


--
-- Name: dtb_layout id; Type: DEFAULT; Schema: public; Owner: dbuser
--

ALTER TABLE ONLY public.dtb_layout ALTER COLUMN id SET DEFAULT nextval('public.dtb_layout_id_seq'::regclass);


--
-- Name: dtb_login_history id; Type: DEFAULT; Schema: public; Owner: dbuser
--

ALTER TABLE ONLY public.dtb_login_history ALTER COLUMN id SET DEFAULT nextval('public.dtb_login_history_id_seq'::regclass);


--
-- Name: dtb_mail_history id; Type: DEFAULT; Schema: public; Owner: dbuser
--

ALTER TABLE ONLY public.dtb_mail_history ALTER COLUMN id SET DEFAULT nextval('public.dtb_mail_history_id_seq'::regclass);


--
-- Name: dtb_mail_template id; Type: DEFAULT; Schema: public; Owner: dbuser
--

ALTER TABLE ONLY public.dtb_mail_template ALTER COLUMN id SET DEFAULT nextval('public.dtb_mail_template_id_seq'::regclass);


--
-- Name: dtb_member id; Type: DEFAULT; Schema: public; Owner: dbuser
--

ALTER TABLE ONLY public.dtb_member ALTER COLUMN id SET DEFAULT nextval('public.dtb_member_id_seq'::regclass);


--
-- Name: dtb_news id; Type: DEFAULT; Schema: public; Owner: dbuser
--

ALTER TABLE ONLY public.dtb_news ALTER COLUMN id SET DEFAULT nextval('public.dtb_news_id_seq'::regclass);


--
-- Name: dtb_order id; Type: DEFAULT; Schema: public; Owner: dbuser
--

ALTER TABLE ONLY public.dtb_order ALTER COLUMN id SET DEFAULT nextval('public.dtb_order_id_seq'::regclass);


--
-- Name: dtb_order_item id; Type: DEFAULT; Schema: public; Owner: dbuser
--

ALTER TABLE ONLY public.dtb_order_item ALTER COLUMN id SET DEFAULT nextval('public.dtb_order_item_id_seq'::regclass);


--
-- Name: dtb_page id; Type: DEFAULT; Schema: public; Owner: dbuser
--

ALTER TABLE ONLY public.dtb_page ALTER COLUMN id SET DEFAULT nextval('public.dtb_page_id_seq'::regclass);


--
-- Name: dtb_payment id; Type: DEFAULT; Schema: public; Owner: dbuser
--

ALTER TABLE ONLY public.dtb_payment ALTER COLUMN id SET DEFAULT nextval('public.dtb_payment_id_seq'::regclass);


--
-- Name: dtb_plugin id; Type: DEFAULT; Schema: public; Owner: dbuser
--

ALTER TABLE ONLY public.dtb_plugin ALTER COLUMN id SET DEFAULT nextval('public.dtb_plugin_id_seq'::regclass);


--
-- Name: dtb_product id; Type: DEFAULT; Schema: public; Owner: dbuser
--

ALTER TABLE ONLY public.dtb_product ALTER COLUMN id SET DEFAULT nextval('public.dtb_product_id_seq'::regclass);


--
-- Name: dtb_product_class id; Type: DEFAULT; Schema: public; Owner: dbuser
--

ALTER TABLE ONLY public.dtb_product_class ALTER COLUMN id SET DEFAULT nextval('public.dtb_product_class_id_seq'::regclass);


--
-- Name: dtb_product_image id; Type: DEFAULT; Schema: public; Owner: dbuser
--

ALTER TABLE ONLY public.dtb_product_image ALTER COLUMN id SET DEFAULT nextval('public.dtb_product_image_id_seq'::regclass);


--
-- Name: dtb_product_stock id; Type: DEFAULT; Schema: public; Owner: dbuser
--

ALTER TABLE ONLY public.dtb_product_stock ALTER COLUMN id SET DEFAULT nextval('public.dtb_product_stock_id_seq'::regclass);


--
-- Name: dtb_product_tag id; Type: DEFAULT; Schema: public; Owner: dbuser
--

ALTER TABLE ONLY public.dtb_product_tag ALTER COLUMN id SET DEFAULT nextval('public.dtb_product_tag_id_seq'::regclass);


--
-- Name: dtb_shipping id; Type: DEFAULT; Schema: public; Owner: dbuser
--

ALTER TABLE ONLY public.dtb_shipping ALTER COLUMN id SET DEFAULT nextval('public.dtb_shipping_id_seq'::regclass);


--
-- Name: dtb_tag id; Type: DEFAULT; Schema: public; Owner: dbuser
--

ALTER TABLE ONLY public.dtb_tag ALTER COLUMN id SET DEFAULT nextval('public.dtb_tag_id_seq'::regclass);


--
-- Name: dtb_tax_rule id; Type: DEFAULT; Schema: public; Owner: dbuser
--

ALTER TABLE ONLY public.dtb_tax_rule ALTER COLUMN id SET DEFAULT nextval('public.dtb_tax_rule_id_seq'::regclass);


--
-- Name: dtb_template id; Type: DEFAULT; Schema: public; Owner: dbuser
--

ALTER TABLE ONLY public.dtb_template ALTER COLUMN id SET DEFAULT nextval('public.dtb_template_id_seq'::regclass);


--
-- Data for Name: dtb_authority_role; Type: TABLE DATA; Schema: public; Owner: dbuser
--



--
-- Data for Name: dtb_base_info; Type: TABLE DATA; Schema: public; Owner: dbuser
--

INSERT INTO public.dtb_base_info VALUES (1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'admin@example.com', 'admin@example.com', 'admin@example.com', 'admin@example.com', 'EC-CUBE SHOP', NULL, NULL, '2022-06-30 07:31:44+00', NULL, NULL, NULL, NULL, true, false, true, false, false, true, true, NULL, NULL, true, 1, 1, 'baseinfo');


--
-- Data for Name: dtb_block; Type: TABLE DATA; Schema: public; Owner: dbuser
--

INSERT INTO public.dtb_block VALUES (1, 10, 'カート', 'cart', false, false, '2017-03-07 10:14:52+00', '2017-03-07 10:14:52+00', 'block');
INSERT INTO public.dtb_block VALUES (2, 10, 'カテゴリ', 'category', false, false, '2017-03-07 10:14:52+00', '2017-03-07 10:14:52+00', 'block');
INSERT INTO public.dtb_block VALUES (3, 10, 'カテゴリナビ(PC)', 'category_nav_pc', false, false, '2017-03-07 10:14:52+00', '2017-03-07 10:14:52+00', 'block');
INSERT INTO public.dtb_block VALUES (4, 10, 'カテゴリナビ(SP)', 'category_nav_sp', false, false, '2017-03-07 10:14:52+00', '2017-03-07 10:14:52+00', 'block');
INSERT INTO public.dtb_block VALUES (5, 10, '新入荷商品特集', 'eyecatch', false, false, '2017-03-07 10:14:52+00', '2017-03-07 10:14:52+00', 'block');
INSERT INTO public.dtb_block VALUES (6, 10, 'フッター', 'footer', false, false, '2017-03-07 10:14:52+00', '2017-03-07 10:14:52+00', 'block');
INSERT INTO public.dtb_block VALUES (7, 10, 'ヘッダー(商品検索・ログインナビ・カート)', 'header', false, false, '2017-03-07 10:14:52+00', '2017-03-07 10:14:52+00', 'block');
INSERT INTO public.dtb_block VALUES (8, 10, 'ログインナビ(共通)', 'login', false, false, '2017-03-07 10:14:52+00', '2017-03-07 10:14:52+00', 'block');
INSERT INTO public.dtb_block VALUES (9, 10, 'ログインナビ(SP)', 'login_sp', false, false, '2017-03-07 10:14:52+00', '2017-03-07 10:14:52+00', 'block');
INSERT INTO public.dtb_block VALUES (10, 10, 'ロゴ', 'logo', false, false, '2017-03-07 10:14:52+00', '2017-03-07 10:14:52+00', 'block');
INSERT INTO public.dtb_block VALUES (11, 10, '新着商品', 'new_item', false, false, '2017-03-07 10:14:52+00', '2017-03-07 10:14:52+00', 'block');
INSERT INTO public.dtb_block VALUES (12, 10, '新着情報', 'news', false, false, '2017-03-07 10:14:52+00', '2017-03-07 10:14:52+00', 'block');
INSERT INTO public.dtb_block VALUES (13, 10, '商品検索', 'search_product', true, false, '2017-03-07 10:14:52+00', '2017-03-07 10:14:52+00', 'block');
INSERT INTO public.dtb_block VALUES (14, 10, 'トピック', 'topic', false, false, '2017-03-07 10:14:52+00', '2017-03-07 10:14:52+00', 'block');
INSERT INTO public.dtb_block VALUES (17, 10, 'カレンダー', 'calendar', true, false, '2021-03-16 12:00:00+00', '2021-03-16 12:00:00+00', 'block');


--
-- Data for Name: dtb_block_position; Type: TABLE DATA; Schema: public; Owner: dbuser
--

INSERT INTO public.dtb_block_position VALUES (3, 7, 1, 1, 'blockposition');
INSERT INTO public.dtb_block_position VALUES (3, 10, 1, 2, 'blockposition');
INSERT INTO public.dtb_block_position VALUES (3, 3, 1, 3, 'blockposition');
INSERT INTO public.dtb_block_position VALUES (7, 5, 1, 1, 'blockposition');
INSERT INTO public.dtb_block_position VALUES (7, 14, 1, 2, 'blockposition');
INSERT INTO public.dtb_block_position VALUES (7, 11, 1, 3, 'blockposition');
INSERT INTO public.dtb_block_position VALUES (7, 2, 1, 4, 'blockposition');
INSERT INTO public.dtb_block_position VALUES (7, 12, 1, 5, 'blockposition');
INSERT INTO public.dtb_block_position VALUES (10, 6, 1, 1, 'blockposition');
INSERT INTO public.dtb_block_position VALUES (11, 13, 1, 1, 'blockposition');
INSERT INTO public.dtb_block_position VALUES (11, 4, 1, 2, 'blockposition');
INSERT INTO public.dtb_block_position VALUES (11, 9, 1, 3, 'blockposition');
INSERT INTO public.dtb_block_position VALUES (3, 7, 2, 1, 'blockposition');
INSERT INTO public.dtb_block_position VALUES (3, 10, 2, 2, 'blockposition');
INSERT INTO public.dtb_block_position VALUES (3, 3, 2, 3, 'blockposition');
INSERT INTO public.dtb_block_position VALUES (10, 6, 2, 1, 'blockposition');
INSERT INTO public.dtb_block_position VALUES (11, 13, 2, 1, 'blockposition');
INSERT INTO public.dtb_block_position VALUES (11, 4, 2, 2, 'blockposition');
INSERT INTO public.dtb_block_position VALUES (11, 9, 2, 3, 'blockposition');


--
-- Data for Name: dtb_calendar; Type: TABLE DATA; Schema: public; Owner: dbuser
--



--
-- Data for Name: dtb_cart; Type: TABLE DATA; Schema: public; Owner: dbuser
--



--
-- Data for Name: dtb_cart_item; Type: TABLE DATA; Schema: public; Owner: dbuser
--



--
-- Data for Name: dtb_category; Type: TABLE DATA; Schema: public; Owner: dbuser
--

INSERT INTO public.dtb_category VALUES (1, NULL, NULL, 'ジェラート', 1, 5, '2017-03-07 10:14:52+00', '2017-03-07 10:14:52+00', 'category');
INSERT INTO public.dtb_category VALUES (2, NULL, NULL, '新入荷', 1, 6, '2017-03-07 10:14:52+00', '2017-03-07 10:14:52+00', 'category');
INSERT INTO public.dtb_category VALUES (3, 1, NULL, '彩のデザート', 2, 3, '2017-03-07 10:14:52+00', '2017-03-07 10:14:52+00', 'category');
INSERT INTO public.dtb_category VALUES (4, 3, NULL, 'CUBE', 3, 2, '2017-03-07 10:14:52+00', '2017-03-07 10:14:52+00', 'category');
INSERT INTO public.dtb_category VALUES (5, NULL, NULL, 'アイスサンド', 1, 1, '2017-03-07 10:14:52+00', '2017-03-07 10:14:52+00', 'category');
INSERT INTO public.dtb_category VALUES (6, 5, NULL, 'フルーツ', 2, 4, '2017-03-07 10:14:52+00', '2017-03-07 10:14:52+00', 'category');


--
-- Data for Name: dtb_class_category; Type: TABLE DATA; Schema: public; Owner: dbuser
--

INSERT INTO public.dtb_class_category VALUES (1, 1, NULL, 'チョコ', 'チョコ', 3, true, '2017-03-07 10:14:52+00', '2017-03-07 10:14:52+00', 'classcategory');
INSERT INTO public.dtb_class_category VALUES (2, 1, NULL, '抹茶', '抹茶', 2, true, '2017-03-07 10:14:52+00', '2017-03-07 10:14:52+00', 'classcategory');
INSERT INTO public.dtb_class_category VALUES (3, 1, NULL, 'バニラ', 'バニラ', 1, true, '2017-03-07 10:14:52+00', '2017-03-07 10:14:52+00', 'classcategory');
INSERT INTO public.dtb_class_category VALUES (4, 2, NULL, '16mm × 16mm', '16mm × 16mm', 3, true, '2017-03-07 10:14:52+00', '2017-03-07 10:14:52+00', 'classcategory');
INSERT INTO public.dtb_class_category VALUES (5, 2, NULL, '32mm × 32mm', '32mm × 32mm', 2, true, '2017-03-07 10:14:52+00', '2017-03-07 10:14:52+00', 'classcategory');
INSERT INTO public.dtb_class_category VALUES (6, 2, NULL, '64cm × 64cm', '64cm × 64cm', 1, true, '2017-03-07 10:14:52+00', '2017-03-07 10:14:52+00', 'classcategory');


--
-- Data for Name: dtb_class_name; Type: TABLE DATA; Schema: public; Owner: dbuser
--

INSERT INTO public.dtb_class_name VALUES (1, NULL, 'CUBE用味', 'フレーバー', 1, '2017-03-07 10:14:52+00', '2017-03-07 10:14:52+00', 'classname');
INSERT INTO public.dtb_class_name VALUES (2, NULL, 'CUBE用サイズ', 'サイズ', 2, '2017-03-07 10:14:52+00', '2017-03-07 10:14:52+00', 'classname');


--
-- Data for Name: dtb_csv; Type: TABLE DATA; Schema: public; Owner: dbuser
--

INSERT INTO public.dtb_csv VALUES (1, 1, NULL, 'Eccube\\Entity\\Product', 'id', NULL, '商品ID', 1, true, '2017-03-07 10:14:00+00', '2017-03-07 10:14:00+00', 'csv');
INSERT INTO public.dtb_csv VALUES (2, 1, NULL, 'Eccube\\Entity\\Product', 'Status', 'id', '公開ステータス(ID)', 2, true, '2017-03-07 10:14:00+00', '2017-03-07 10:14:00+00', 'csv');
INSERT INTO public.dtb_csv VALUES (3, 1, NULL, 'Eccube\\Entity\\Product', 'Status', 'name', '公開ステータス(名称)', 3, true, '2017-03-07 10:14:00+00', '2017-03-07 10:14:00+00', 'csv');
INSERT INTO public.dtb_csv VALUES (4, 1, NULL, 'Eccube\\Entity\\Product', 'name', NULL, '商品名', 4, true, '2017-03-07 10:14:00+00', '2017-03-07 10:14:00+00', 'csv');
INSERT INTO public.dtb_csv VALUES (5, 1, NULL, 'Eccube\\Entity\\Product', 'note', NULL, 'ショップ用メモ欄', 5, true, '2017-03-07 10:14:00+00', '2017-03-07 10:14:00+00', 'csv');
INSERT INTO public.dtb_csv VALUES (6, 1, NULL, 'Eccube\\Entity\\Product', 'description_list', NULL, '商品説明(一覧)', 6, true, '2017-03-07 10:14:00+00', '2017-03-07 10:14:00+00', 'csv');
INSERT INTO public.dtb_csv VALUES (7, 1, NULL, 'Eccube\\Entity\\Product', 'description_detail', NULL, '商品説明(詳細)', 7, true, '2017-03-07 10:14:00+00', '2017-03-07 10:14:00+00', 'csv');
INSERT INTO public.dtb_csv VALUES (8, 1, NULL, 'Eccube\\Entity\\Product', 'search_word', NULL, '検索ワード', 8, true, '2017-03-07 10:14:00+00', '2017-03-07 10:14:00+00', 'csv');
INSERT INTO public.dtb_csv VALUES (9, 1, NULL, 'Eccube\\Entity\\Product', 'free_area', NULL, 'フリーエリア', 9, true, '2017-03-07 10:14:00+00', '2017-03-07 10:14:00+00', 'csv');
INSERT INTO public.dtb_csv VALUES (10, 1, NULL, 'Eccube\\Entity\\ProductClass', 'id', NULL, '商品規格ID', 10, true, '2017-03-07 10:14:00+00', '2017-03-07 10:14:00+00', 'csv');
INSERT INTO public.dtb_csv VALUES (11, 1, NULL, 'Eccube\\Entity\\ProductClass', 'SaleType', 'id', '販売種別(ID)', 11, true, '2017-03-07 10:14:00+00', '2017-03-07 10:14:00+00', 'csv');
INSERT INTO public.dtb_csv VALUES (12, 1, NULL, 'Eccube\\Entity\\ProductClass', 'SaleType', 'name', '販売種別(名称)', 12, true, '2017-03-07 10:14:00+00', '2017-03-07 10:14:00+00', 'csv');
INSERT INTO public.dtb_csv VALUES (13, 1, NULL, 'Eccube\\Entity\\ProductClass', 'ClassCategory1', 'id', '規格分類1(ID)', 13, true, '2017-03-07 10:14:00+00', '2017-03-07 10:14:00+00', 'csv');
INSERT INTO public.dtb_csv VALUES (14, 1, NULL, 'Eccube\\Entity\\ProductClass', 'ClassCategory1', 'name', '規格分類1(名称)', 14, true, '2017-03-07 10:14:00+00', '2017-03-07 10:14:00+00', 'csv');
INSERT INTO public.dtb_csv VALUES (15, 1, NULL, 'Eccube\\Entity\\ProductClass', 'ClassCategory2', 'id', '規格分類2(ID)', 15, true, '2017-03-07 10:14:00+00', '2017-03-07 10:14:00+00', 'csv');
INSERT INTO public.dtb_csv VALUES (16, 1, NULL, 'Eccube\\Entity\\ProductClass', 'ClassCategory2', 'name', '規格分類2(名称)', 16, true, '2017-03-07 10:14:00+00', '2017-03-07 10:14:00+00', 'csv');
INSERT INTO public.dtb_csv VALUES (17, 1, NULL, 'Eccube\\Entity\\ProductClass', 'DeliveryDuration', 'id', '発送日目安(ID)', 17, true, '2017-03-07 10:14:00+00', '2017-03-07 10:14:00+00', 'csv');
INSERT INTO public.dtb_csv VALUES (18, 1, NULL, 'Eccube\\Entity\\ProductClass', 'DeliveryDuration', 'name', '発送日目安(名称)', 18, true, '2017-03-07 10:14:00+00', '2017-03-07 10:14:00+00', 'csv');
INSERT INTO public.dtb_csv VALUES (19, 1, NULL, 'Eccube\\Entity\\ProductClass', 'code', NULL, '商品コード', 19, true, '2017-03-07 10:14:00+00', '2017-03-07 10:14:00+00', 'csv');
INSERT INTO public.dtb_csv VALUES (20, 1, NULL, 'Eccube\\Entity\\ProductClass', 'stock', NULL, '在庫数', 20, true, '2017-03-07 10:14:00+00', '2017-03-07 10:14:00+00', 'csv');
INSERT INTO public.dtb_csv VALUES (21, 1, NULL, 'Eccube\\Entity\\ProductClass', 'stock_unlimited', NULL, '在庫数無制限フラグ', 21, true, '2017-03-07 10:14:00+00', '2017-03-07 10:14:00+00', 'csv');
INSERT INTO public.dtb_csv VALUES (22, 1, NULL, 'Eccube\\Entity\\ProductClass', 'sale_limit', NULL, '販売制限数', 22, true, '2017-03-07 10:14:00+00', '2017-03-07 10:14:00+00', 'csv');
INSERT INTO public.dtb_csv VALUES (23, 1, NULL, 'Eccube\\Entity\\ProductClass', 'price01', NULL, '通常価格', 23, true, '2017-03-07 10:14:00+00', '2017-03-07 10:14:00+00', 'csv');
INSERT INTO public.dtb_csv VALUES (24, 1, NULL, 'Eccube\\Entity\\ProductClass', 'price02', NULL, '販売価格', 24, true, '2017-03-07 10:14:00+00', '2017-03-07 10:14:00+00', 'csv');
INSERT INTO public.dtb_csv VALUES (25, 1, NULL, 'Eccube\\Entity\\ProductClass', 'delivery_fee', NULL, '送料', 25, false, '2017-03-07 10:14:00+00', '2017-03-07 10:14:00+00', 'csv');
INSERT INTO public.dtb_csv VALUES (26, 1, NULL, 'Eccube\\Entity\\Product', 'ProductImage', 'file_name', '商品画像', 26, true, '2017-03-07 10:14:00+00', '2017-03-07 10:14:00+00', 'csv');
INSERT INTO public.dtb_csv VALUES (27, 1, NULL, 'Eccube\\Entity\\Product', 'ProductCategories', 'category_id', '商品カテゴリ(ID)', 27, true, '2017-03-07 10:14:00+00', '2017-03-07 10:14:00+00', 'csv');
INSERT INTO public.dtb_csv VALUES (28, 1, NULL, 'Eccube\\Entity\\Product', 'ProductCategories', 'Category', '商品カテゴリ(名称)', 28, true, '2017-03-07 10:14:00+00', '2017-03-07 10:14:00+00', 'csv');
INSERT INTO public.dtb_csv VALUES (29, 1, NULL, 'Eccube\\Entity\\Product', 'ProductTag', 'tag_id', 'タグ(ID)', 29, true, '2017-03-07 10:14:00+00', '2017-03-07 10:14:00+00', 'csv');
INSERT INTO public.dtb_csv VALUES (30, 1, NULL, 'Eccube\\Entity\\Product', 'ProductTag', 'Tag', 'タグ(名称)', 30, true, '2017-03-07 10:14:00+00', '2017-03-07 10:14:00+00', 'csv');
INSERT INTO public.dtb_csv VALUES (31, 2, NULL, 'Eccube\\Entity\\Customer', 'id', NULL, '会員ID', 1, true, '2017-03-07 10:14:00+00', '2017-03-07 10:14:00+00', 'csv');
INSERT INTO public.dtb_csv VALUES (32, 2, NULL, 'Eccube\\Entity\\Customer', 'name01', NULL, 'お名前(姓)', 2, true, '2017-03-07 10:14:00+00', '2017-03-07 10:14:00+00', 'csv');
INSERT INTO public.dtb_csv VALUES (33, 2, NULL, 'Eccube\\Entity\\Customer', 'name02', NULL, 'お名前(名)', 3, true, '2017-03-07 10:14:00+00', '2017-03-07 10:14:00+00', 'csv');
INSERT INTO public.dtb_csv VALUES (34, 2, NULL, 'Eccube\\Entity\\Customer', 'kana01', NULL, 'お名前(セイ)', 4, true, '2017-03-07 10:14:00+00', '2017-03-07 10:14:00+00', 'csv');
INSERT INTO public.dtb_csv VALUES (35, 2, NULL, 'Eccube\\Entity\\Customer', 'kana02', NULL, 'お名前(メイ)', 5, true, '2017-03-07 10:14:00+00', '2017-03-07 10:14:00+00', 'csv');
INSERT INTO public.dtb_csv VALUES (36, 2, NULL, 'Eccube\\Entity\\Customer', 'company_name', NULL, '会社名', 6, true, '2017-03-07 10:14:00+00', '2017-03-07 10:14:00+00', 'csv');
INSERT INTO public.dtb_csv VALUES (37, 2, NULL, 'Eccube\\Entity\\Customer', 'postal_code', NULL, '郵便番号', 7, true, '2017-03-07 10:14:00+00', '2017-03-07 10:14:00+00', 'csv');
INSERT INTO public.dtb_csv VALUES (38, 2, NULL, 'Eccube\\Entity\\Customer', 'Pref', 'id', '都道府県(ID)', 9, true, '2017-03-07 10:14:00+00', '2017-03-07 10:14:00+00', 'csv');
INSERT INTO public.dtb_csv VALUES (39, 2, NULL, 'Eccube\\Entity\\Customer', 'Pref', 'name', '都道府県(名称)', 10, true, '2017-03-07 10:14:00+00', '2017-03-07 10:14:00+00', 'csv');
INSERT INTO public.dtb_csv VALUES (40, 2, NULL, 'Eccube\\Entity\\Customer', 'addr01', NULL, '住所1', 11, true, '2017-03-07 10:14:00+00', '2017-03-07 10:14:00+00', 'csv');
INSERT INTO public.dtb_csv VALUES (41, 2, NULL, 'Eccube\\Entity\\Customer', 'addr02', NULL, '住所2', 12, true, '2017-03-07 10:14:00+00', '2017-03-07 10:14:00+00', 'csv');
INSERT INTO public.dtb_csv VALUES (42, 2, NULL, 'Eccube\\Entity\\Customer', 'email', NULL, 'メールアドレス', 13, true, '2017-03-07 10:14:00+00', '2017-03-07 10:14:00+00', 'csv');
INSERT INTO public.dtb_csv VALUES (43, 2, NULL, 'Eccube\\Entity\\Customer', 'phone_number', NULL, 'TEL', 14, true, '2017-03-07 10:14:00+00', '2017-03-07 10:14:00+00', 'csv');
INSERT INTO public.dtb_csv VALUES (44, 2, NULL, 'Eccube\\Entity\\Customer', 'Sex', 'id', '性別(ID)', 20, true, '2017-03-07 10:14:00+00', '2017-03-07 10:14:00+00', 'csv');
INSERT INTO public.dtb_csv VALUES (45, 2, NULL, 'Eccube\\Entity\\Customer', 'Sex', 'name', '性別(名称)', 21, true, '2017-03-07 10:14:00+00', '2017-03-07 10:14:00+00', 'csv');
INSERT INTO public.dtb_csv VALUES (46, 2, NULL, 'Eccube\\Entity\\Customer', 'Job', 'id', '職業(ID)', 22, true, '2017-03-07 10:14:00+00', '2017-03-07 10:14:00+00', 'csv');
INSERT INTO public.dtb_csv VALUES (47, 2, NULL, 'Eccube\\Entity\\Customer', 'Job', 'name', '職業(名称)', 23, true, '2017-03-07 10:14:00+00', '2017-03-07 10:14:00+00', 'csv');
INSERT INTO public.dtb_csv VALUES (48, 2, NULL, 'Eccube\\Entity\\Customer', 'birth', NULL, '誕生日', 24, true, '2017-03-07 10:14:00+00', '2017-03-07 10:14:00+00', 'csv');
INSERT INTO public.dtb_csv VALUES (49, 2, NULL, 'Eccube\\Entity\\Customer', 'first_buy_date', NULL, '初回購入日', 25, true, '2017-03-07 10:14:00+00', '2017-03-07 10:14:00+00', 'csv');
INSERT INTO public.dtb_csv VALUES (50, 2, NULL, 'Eccube\\Entity\\Customer', 'last_buy_date', NULL, '最終購入日', 26, true, '2017-03-07 10:14:00+00', '2017-03-07 10:14:00+00', 'csv');
INSERT INTO public.dtb_csv VALUES (51, 2, NULL, 'Eccube\\Entity\\Customer', 'buy_times', NULL, '購入回数', 27, true, '2017-03-07 10:14:00+00', '2017-03-07 10:14:00+00', 'csv');
INSERT INTO public.dtb_csv VALUES (52, 2, NULL, 'Eccube\\Entity\\Customer', 'note', NULL, 'ショップ用メモ欄', 28, true, '2017-03-07 10:14:00+00', '2017-03-07 10:14:00+00', 'csv');
INSERT INTO public.dtb_csv VALUES (53, 2, NULL, 'Eccube\\Entity\\Customer', 'Status', 'id', '会員ステータス(ID)', 29, true, '2017-03-07 10:14:00+00', '2017-03-07 10:14:00+00', 'csv');
INSERT INTO public.dtb_csv VALUES (54, 2, NULL, 'Eccube\\Entity\\Customer', 'Status', 'name', '会員ステータス(名称)', 30, true, '2017-03-07 10:14:00+00', '2017-03-07 10:14:00+00', 'csv');
INSERT INTO public.dtb_csv VALUES (55, 2, NULL, 'Eccube\\Entity\\Customer', 'create_date', NULL, '登録日', 31, true, '2017-03-07 10:14:00+00', '2017-03-07 10:14:00+00', 'csv');
INSERT INTO public.dtb_csv VALUES (56, 2, NULL, 'Eccube\\Entity\\Customer', 'update_date', NULL, '更新日', 32, true, '2017-03-07 10:14:00+00', '2017-03-07 10:14:00+00', 'csv');
INSERT INTO public.dtb_csv VALUES (57, 3, NULL, 'Eccube\\Entity\\Order', 'id', NULL, '注文ID', 1, true, '2017-03-07 10:14:00+00', '2017-03-07 10:14:00+00', 'csv');
INSERT INTO public.dtb_csv VALUES (58, 3, NULL, 'Eccube\\Entity\\Order', 'order_no', NULL, '注文番号', 2, true, '2017-03-07 10:14:00+00', '2017-03-07 10:14:00+00', 'csv');
INSERT INTO public.dtb_csv VALUES (59, 3, NULL, 'Eccube\\Entity\\Order', 'Customer', 'id', '会員ID', 3, true, '2017-03-07 10:14:00+00', '2017-03-07 10:14:00+00', 'csv');
INSERT INTO public.dtb_csv VALUES (60, 3, NULL, 'Eccube\\Entity\\Order', 'name01', NULL, 'お名前(姓)', 4, true, '2017-03-07 10:14:00+00', '2017-03-07 10:14:00+00', 'csv');
INSERT INTO public.dtb_csv VALUES (61, 3, NULL, 'Eccube\\Entity\\Order', 'name02', NULL, 'お名前(名)', 5, true, '2017-03-07 10:14:00+00', '2017-03-07 10:14:00+00', 'csv');
INSERT INTO public.dtb_csv VALUES (62, 3, NULL, 'Eccube\\Entity\\Order', 'kana01', NULL, 'お名前(セイ)', 6, true, '2017-03-07 10:14:00+00', '2017-03-07 10:14:00+00', 'csv');
INSERT INTO public.dtb_csv VALUES (63, 3, NULL, 'Eccube\\Entity\\Order', 'kana02', NULL, 'お名前(メイ)', 7, true, '2017-03-07 10:14:00+00', '2017-03-07 10:14:00+00', 'csv');
INSERT INTO public.dtb_csv VALUES (64, 3, NULL, 'Eccube\\Entity\\Order', 'company_name', NULL, '会社名', 8, true, '2017-03-07 10:14:00+00', '2017-03-07 10:14:00+00', 'csv');
INSERT INTO public.dtb_csv VALUES (65, 3, NULL, 'Eccube\\Entity\\Order', 'postal_code', NULL, '郵便番号', 9, true, '2017-03-07 10:14:00+00', '2017-03-07 10:14:00+00', 'csv');
INSERT INTO public.dtb_csv VALUES (66, 3, NULL, 'Eccube\\Entity\\Order', 'Pref', 'id', '都道府県(ID)', 10, true, '2017-03-07 10:14:00+00', '2017-03-07 10:14:00+00', 'csv');
INSERT INTO public.dtb_csv VALUES (67, 3, NULL, 'Eccube\\Entity\\Order', 'Pref', 'name', '都道府県(名称)', 11, true, '2017-03-07 10:14:00+00', '2017-03-07 10:14:00+00', 'csv');
INSERT INTO public.dtb_csv VALUES (68, 3, NULL, 'Eccube\\Entity\\Order', 'addr01', NULL, '住所1', 12, true, '2017-03-07 10:14:00+00', '2017-03-07 10:14:00+00', 'csv');
INSERT INTO public.dtb_csv VALUES (69, 3, NULL, 'Eccube\\Entity\\Order', 'addr02', NULL, '住所2', 13, true, '2017-03-07 10:14:00+00', '2017-03-07 10:14:00+00', 'csv');
INSERT INTO public.dtb_csv VALUES (70, 3, NULL, 'Eccube\\Entity\\Order', 'email', NULL, 'メールアドレス', 14, true, '2017-03-07 10:14:00+00', '2017-03-07 10:14:00+00', 'csv');
INSERT INTO public.dtb_csv VALUES (71, 3, NULL, 'Eccube\\Entity\\Order', 'phone_number', NULL, 'TEL', 15, true, '2017-03-07 10:14:00+00', '2017-03-07 10:14:00+00', 'csv');
INSERT INTO public.dtb_csv VALUES (72, 3, NULL, 'Eccube\\Entity\\Order', 'Sex', 'id', '性別(ID)', 16, true, '2017-03-07 10:14:00+00', '2017-03-07 10:14:00+00', 'csv');
INSERT INTO public.dtb_csv VALUES (73, 3, NULL, 'Eccube\\Entity\\Order', 'Sex', 'name', '性別(名称)', 17, true, '2017-03-07 10:14:00+00', '2017-03-07 10:14:00+00', 'csv');
INSERT INTO public.dtb_csv VALUES (74, 3, NULL, 'Eccube\\Entity\\Order', 'Job', 'id', '職業(ID)', 18, true, '2017-03-07 10:14:00+00', '2017-03-07 10:14:00+00', 'csv');
INSERT INTO public.dtb_csv VALUES (75, 3, NULL, 'Eccube\\Entity\\Order', 'Job', 'name', '職業(名称)', 19, true, '2017-03-07 10:14:00+00', '2017-03-07 10:14:00+00', 'csv');
INSERT INTO public.dtb_csv VALUES (76, 3, NULL, 'Eccube\\Entity\\Order', 'birth', NULL, '誕生日', 20, true, '2017-03-07 10:14:00+00', '2017-03-07 10:14:00+00', 'csv');
INSERT INTO public.dtb_csv VALUES (77, 3, NULL, 'Eccube\\Entity\\Order', 'note', NULL, 'ショップ用メモ欄', 21, true, '2017-03-07 10:14:00+00', '2017-03-07 10:14:00+00', 'csv');
INSERT INTO public.dtb_csv VALUES (78, 3, NULL, 'Eccube\\Entity\\Order', 'subtotal', NULL, '小計', 22, true, '2017-03-07 10:14:00+00', '2017-03-07 10:14:00+00', 'csv');
INSERT INTO public.dtb_csv VALUES (79, 3, NULL, 'Eccube\\Entity\\Order', 'discount', NULL, '値引き', 23, true, '2017-03-07 10:14:00+00', '2017-03-07 10:14:00+00', 'csv');
INSERT INTO public.dtb_csv VALUES (80, 3, NULL, 'Eccube\\Entity\\Order', 'delivery_fee_total', NULL, '送料', 24, true, '2017-03-07 10:14:00+00', '2017-03-07 10:14:00+00', 'csv');
INSERT INTO public.dtb_csv VALUES (81, 3, NULL, 'Eccube\\Entity\\Order', 'tax', NULL, '税金', 25, true, '2017-03-07 10:14:00+00', '2017-03-07 10:14:00+00', 'csv');
INSERT INTO public.dtb_csv VALUES (82, 3, NULL, 'Eccube\\Entity\\Order', 'total', NULL, '合計', 26, true, '2017-03-07 10:14:00+00', '2017-03-07 10:14:00+00', 'csv');
INSERT INTO public.dtb_csv VALUES (83, 3, NULL, 'Eccube\\Entity\\Order', 'payment_total', NULL, '支払合計', 27, true, '2017-03-07 10:14:00+00', '2017-03-07 10:14:00+00', 'csv');
INSERT INTO public.dtb_csv VALUES (84, 3, NULL, 'Eccube\\Entity\\Order', 'OrderStatus', 'id', '対応状況(ID)', 28, true, '2017-03-07 10:14:00+00', '2017-03-07 10:14:00+00', 'csv');
INSERT INTO public.dtb_csv VALUES (85, 3, NULL, 'Eccube\\Entity\\Order', 'OrderStatus', 'name', '対応状況(名称)', 29, true, '2017-03-07 10:14:00+00', '2017-03-07 10:14:00+00', 'csv');
INSERT INTO public.dtb_csv VALUES (86, 3, NULL, 'Eccube\\Entity\\Order', 'Payment', 'id', '支払方法(ID)', 30, true, '2017-03-07 10:14:00+00', '2017-03-07 10:14:00+00', 'csv');
INSERT INTO public.dtb_csv VALUES (87, 3, NULL, 'Eccube\\Entity\\Order', 'payment_method', NULL, '支払方法(名称)', 31, true, '2017-03-07 10:14:00+00', '2017-03-07 10:14:00+00', 'csv');
INSERT INTO public.dtb_csv VALUES (88, 3, NULL, 'Eccube\\Entity\\Order', 'order_date', NULL, '受注日', 32, true, '2017-03-07 10:14:00+00', '2017-03-07 10:14:00+00', 'csv');
INSERT INTO public.dtb_csv VALUES (89, 3, NULL, 'Eccube\\Entity\\Order', 'payment_date', NULL, '入金日', 33, true, '2017-03-07 10:14:00+00', '2017-03-07 10:14:00+00', 'csv');
INSERT INTO public.dtb_csv VALUES (90, 3, NULL, 'Eccube\\Entity\\OrderItem', 'id', NULL, '注文詳細ID', 34, true, '2017-03-07 10:14:00+00', '2017-03-07 10:14:00+00', 'csv');
INSERT INTO public.dtb_csv VALUES (91, 3, NULL, 'Eccube\\Entity\\OrderItem', 'Product', 'id', '商品ID', 35, true, '2017-03-07 10:14:00+00', '2017-03-07 10:14:00+00', 'csv');
INSERT INTO public.dtb_csv VALUES (92, 3, NULL, 'Eccube\\Entity\\OrderItem', 'ProductClass', 'id', '商品規格ID', 36, true, '2017-03-07 10:14:00+00', '2017-03-07 10:14:00+00', 'csv');
INSERT INTO public.dtb_csv VALUES (93, 3, NULL, 'Eccube\\Entity\\OrderItem', 'product_name', NULL, '商品名', 37, true, '2017-03-07 10:14:00+00', '2017-03-07 10:14:00+00', 'csv');
INSERT INTO public.dtb_csv VALUES (94, 3, NULL, 'Eccube\\Entity\\OrderItem', 'product_code', NULL, '商品コード', 38, true, '2017-03-07 10:14:00+00', '2017-03-07 10:14:00+00', 'csv');
INSERT INTO public.dtb_csv VALUES (95, 3, NULL, 'Eccube\\Entity\\OrderItem', 'class_name1', NULL, '規格名1', 39, true, '2017-03-07 10:14:00+00', '2017-03-07 10:14:00+00', 'csv');
INSERT INTO public.dtb_csv VALUES (96, 3, NULL, 'Eccube\\Entity\\OrderItem', 'class_name2', NULL, '規格名2', 40, true, '2017-03-07 10:14:00+00', '2017-03-07 10:14:00+00', 'csv');
INSERT INTO public.dtb_csv VALUES (97, 3, NULL, 'Eccube\\Entity\\OrderItem', 'class_category_name1', NULL, '規格分類名1', 41, true, '2017-03-07 10:14:00+00', '2017-03-07 10:14:00+00', 'csv');
INSERT INTO public.dtb_csv VALUES (98, 3, NULL, 'Eccube\\Entity\\OrderItem', 'class_category_name2', NULL, '規格分類名2', 42, true, '2017-03-07 10:14:00+00', '2017-03-07 10:14:00+00', 'csv');
INSERT INTO public.dtb_csv VALUES (99, 3, NULL, 'Eccube\\Entity\\OrderItem', 'price', NULL, '価格', 43, true, '2017-03-07 10:14:00+00', '2017-03-07 10:14:00+00', 'csv');
INSERT INTO public.dtb_csv VALUES (100, 3, NULL, 'Eccube\\Entity\\OrderItem', 'quantity', NULL, '個数', 44, true, '2017-03-07 10:14:00+00', '2017-03-07 10:14:00+00', 'csv');
INSERT INTO public.dtb_csv VALUES (101, 3, NULL, 'Eccube\\Entity\\OrderItem', 'tax_rate', NULL, '税率', 45, true, '2017-03-07 10:14:00+00', '2017-03-07 10:14:00+00', 'csv');
INSERT INTO public.dtb_csv VALUES (102, 3, NULL, 'Eccube\\Entity\\OrderItem', 'tax_rule', NULL, '税率ルール(ID)', 46, true, '2017-03-07 10:14:00+00', '2017-03-07 10:14:00+00', 'csv');
INSERT INTO public.dtb_csv VALUES (103, 3, NULL, 'Eccube\\Entity\\OrderItem', 'OrderItemType', 'id', '明細区分(ID)', 47, true, '2018-07-23 09:00:00+00', '2018-07-23 09:00:00+00', 'csv');
INSERT INTO public.dtb_csv VALUES (104, 3, NULL, 'Eccube\\Entity\\OrderItem', 'OrderItemType', 'name', '明細区分(名称)', 48, true, '2018-07-23 09:00:00+00', '2018-07-23 09:00:00+00', 'csv');
INSERT INTO public.dtb_csv VALUES (105, 3, NULL, 'Eccube\\Entity\\Shipping', 'id', NULL, '出荷ID', 49, true, '2017-03-07 10:14:00+00', '2017-03-07 10:14:00+00', 'csv');
INSERT INTO public.dtb_csv VALUES (106, 3, NULL, 'Eccube\\Entity\\Shipping', 'name01', NULL, '配送先_お名前(姓)', 50, true, '2017-03-07 10:14:00+00', '2017-03-07 10:14:00+00', 'csv');
INSERT INTO public.dtb_csv VALUES (107, 3, NULL, 'Eccube\\Entity\\Shipping', 'name02', NULL, '配送先_お名前(名)', 51, true, '2017-03-07 10:14:00+00', '2017-03-07 10:14:00+00', 'csv');
INSERT INTO public.dtb_csv VALUES (108, 3, NULL, 'Eccube\\Entity\\Shipping', 'kana01', NULL, '配送先_お名前(セイ)', 52, true, '2017-03-07 10:14:00+00', '2017-03-07 10:14:00+00', 'csv');
INSERT INTO public.dtb_csv VALUES (109, 3, NULL, 'Eccube\\Entity\\Shipping', 'kana02', NULL, '配送先_お名前(メイ)', 53, true, '2017-03-07 10:14:00+00', '2017-03-07 10:14:00+00', 'csv');
INSERT INTO public.dtb_csv VALUES (110, 3, NULL, 'Eccube\\Entity\\Shipping', 'company_name', NULL, '配送先_会社名', 54, true, '2017-03-07 10:14:00+00', '2017-03-07 10:14:00+00', 'csv');
INSERT INTO public.dtb_csv VALUES (111, 3, NULL, 'Eccube\\Entity\\Shipping', 'postal_code', NULL, '配送先_郵便番号', 55, true, '2017-03-07 10:14:00+00', '2017-03-07 10:14:00+00', 'csv');
INSERT INTO public.dtb_csv VALUES (112, 3, NULL, 'Eccube\\Entity\\Shipping', 'Pref', 'id', '配送先_都道府県(ID)', 56, true, '2017-03-07 10:14:00+00', '2017-03-07 10:14:00+00', 'csv');
INSERT INTO public.dtb_csv VALUES (113, 3, NULL, 'Eccube\\Entity\\Shipping', 'Pref', 'name', '配送先_都道府県(名称)', 57, true, '2017-03-07 10:14:00+00', '2017-03-07 10:14:00+00', 'csv');
INSERT INTO public.dtb_csv VALUES (114, 3, NULL, 'Eccube\\Entity\\Shipping', 'addr01', NULL, '配送先_住所1', 58, true, '2017-03-07 10:14:00+00', '2017-03-07 10:14:00+00', 'csv');
INSERT INTO public.dtb_csv VALUES (115, 3, NULL, 'Eccube\\Entity\\Shipping', 'addr02', NULL, '配送先_住所2', 59, true, '2017-03-07 10:14:00+00', '2017-03-07 10:14:00+00', 'csv');
INSERT INTO public.dtb_csv VALUES (116, 3, NULL, 'Eccube\\Entity\\Shipping', 'phone_number', NULL, '配送先_TEL', 60, true, '2017-03-07 10:14:00+00', '2017-03-07 10:14:00+00', 'csv');
INSERT INTO public.dtb_csv VALUES (117, 3, NULL, 'Eccube\\Entity\\Shipping', 'Delivery', 'id', '配送業者(ID)', 61, true, '2017-03-07 10:14:00+00', '2017-03-07 10:14:00+00', 'csv');
INSERT INTO public.dtb_csv VALUES (118, 3, NULL, 'Eccube\\Entity\\Shipping', 'shipping_delivery_name', NULL, '配送業者(名称)', 62, true, '2017-03-07 10:14:00+00', '2017-03-07 10:14:00+00', 'csv');
INSERT INTO public.dtb_csv VALUES (119, 3, NULL, 'Eccube\\Entity\\Shipping', 'time_id', NULL, 'お届け時間ID', 63, true, '2017-03-07 10:14:00+00', '2017-03-07 10:14:00+00', 'csv');
INSERT INTO public.dtb_csv VALUES (120, 3, NULL, 'Eccube\\Entity\\Shipping', 'shipping_delivery_time', NULL, 'お届け時間(名称)', 64, true, '2017-03-07 10:14:00+00', '2017-03-07 10:14:00+00', 'csv');
INSERT INTO public.dtb_csv VALUES (121, 3, NULL, 'Eccube\\Entity\\Shipping', 'shipping_delivery_date', NULL, 'お届け希望日', 65, true, '2017-03-07 10:14:00+00', '2017-03-07 10:14:00+00', 'csv');
INSERT INTO public.dtb_csv VALUES (123, 3, NULL, 'Eccube\\Entity\\Shipping', 'shipping_delivery_fee', NULL, '送料', 67, true, '2017-03-07 10:14:00+00', '2017-03-07 10:14:00+00', 'csv');
INSERT INTO public.dtb_csv VALUES (124, 3, NULL, 'Eccube\\Entity\\Shipping', 'shipping_date', NULL, '発送日', 68, true, '2017-03-07 10:14:00+00', '2017-03-07 10:14:00+00', 'csv');
INSERT INTO public.dtb_csv VALUES (125, 3, NULL, 'Eccube\\Entity\\Shipping', 'tracking_number', NULL, '出荷伝票番号', 69, true, '2017-03-07 10:14:00+00', '2017-03-07 10:14:00+00', 'csv');
INSERT INTO public.dtb_csv VALUES (126, 3, NULL, 'Eccube\\Entity\\Shipping', 'note', NULL, '配達用メモ', 70, true, '2017-03-07 10:14:00+00', '2017-03-07 10:14:00+00', 'csv');
INSERT INTO public.dtb_csv VALUES (127, 3, NULL, 'Eccube\\Entity\\Shipping', 'mail_send_date', NULL, '出荷メール送信日', 71, true, '2017-03-07 10:14:00+00', '2017-03-07 10:14:00+00', 'csv');
INSERT INTO public.dtb_csv VALUES (128, 4, NULL, 'Eccube\\Entity\\Order', 'id', NULL, '注文ID', 1, true, '2017-03-07 10:14:00+00', '2017-03-07 10:14:00+00', 'csv');
INSERT INTO public.dtb_csv VALUES (129, 4, NULL, 'Eccube\\Entity\\Order', 'order_no', NULL, '注文番号', 2, true, '2017-03-07 10:14:00+00', '2017-03-07 10:14:00+00', 'csv');
INSERT INTO public.dtb_csv VALUES (130, 4, NULL, 'Eccube\\Entity\\Order', 'Customer', 'id', '会員ID', 3, true, '2017-03-07 10:14:00+00', '2017-03-07 10:14:00+00', 'csv');
INSERT INTO public.dtb_csv VALUES (131, 4, NULL, 'Eccube\\Entity\\Order', 'name01', NULL, 'お名前(姓)', 4, true, '2017-03-07 10:14:00+00', '2017-03-07 10:14:00+00', 'csv');
INSERT INTO public.dtb_csv VALUES (132, 4, NULL, 'Eccube\\Entity\\Order', 'name02', NULL, 'お名前(名)', 5, true, '2017-03-07 10:14:00+00', '2017-03-07 10:14:00+00', 'csv');
INSERT INTO public.dtb_csv VALUES (133, 4, NULL, 'Eccube\\Entity\\Order', 'kana01', NULL, 'お名前(セイ)', 6, true, '2017-03-07 10:14:00+00', '2017-03-07 10:14:00+00', 'csv');
INSERT INTO public.dtb_csv VALUES (134, 4, NULL, 'Eccube\\Entity\\Order', 'kana02', NULL, 'お名前(メイ)', 7, true, '2017-03-07 10:14:00+00', '2017-03-07 10:14:00+00', 'csv');
INSERT INTO public.dtb_csv VALUES (135, 4, NULL, 'Eccube\\Entity\\Order', 'company_name', NULL, '会社名', 8, true, '2017-03-07 10:14:00+00', '2017-03-07 10:14:00+00', 'csv');
INSERT INTO public.dtb_csv VALUES (136, 4, NULL, 'Eccube\\Entity\\Order', 'postal_code', NULL, '郵便番号', 9, true, '2017-03-07 10:14:00+00', '2017-03-07 10:14:00+00', 'csv');
INSERT INTO public.dtb_csv VALUES (137, 4, NULL, 'Eccube\\Entity\\Order', 'Pref', 'id', '都道府県(ID)', 10, true, '2017-03-07 10:14:00+00', '2017-03-07 10:14:00+00', 'csv');
INSERT INTO public.dtb_csv VALUES (138, 4, NULL, 'Eccube\\Entity\\Order', 'Pref', 'name', '都道府県(名称)', 11, true, '2017-03-07 10:14:00+00', '2017-03-07 10:14:00+00', 'csv');
INSERT INTO public.dtb_csv VALUES (139, 4, NULL, 'Eccube\\Entity\\Order', 'addr01', NULL, '住所1', 12, true, '2017-03-07 10:14:00+00', '2017-03-07 10:14:00+00', 'csv');
INSERT INTO public.dtb_csv VALUES (140, 4, NULL, 'Eccube\\Entity\\Order', 'addr02', NULL, '住所2', 13, true, '2017-03-07 10:14:00+00', '2017-03-07 10:14:00+00', 'csv');
INSERT INTO public.dtb_csv VALUES (141, 4, NULL, 'Eccube\\Entity\\Order', 'email', NULL, 'メールアドレス', 14, true, '2017-03-07 10:14:00+00', '2017-03-07 10:14:00+00', 'csv');
INSERT INTO public.dtb_csv VALUES (142, 4, NULL, 'Eccube\\Entity\\Order', 'phone_number', NULL, 'TEL', 15, true, '2017-03-07 10:14:00+00', '2017-03-07 10:14:00+00', 'csv');
INSERT INTO public.dtb_csv VALUES (143, 4, NULL, 'Eccube\\Entity\\Order', 'Sex', 'id', '性別(ID)', 16, true, '2017-03-07 10:14:00+00', '2017-03-07 10:14:00+00', 'csv');
INSERT INTO public.dtb_csv VALUES (144, 4, NULL, 'Eccube\\Entity\\Order', 'Sex', 'name', '性別(名称)', 17, true, '2017-03-07 10:14:00+00', '2017-03-07 10:14:00+00', 'csv');
INSERT INTO public.dtb_csv VALUES (145, 4, NULL, 'Eccube\\Entity\\Order', 'Job', 'id', '職業(ID)', 18, true, '2017-03-07 10:14:00+00', '2017-03-07 10:14:00+00', 'csv');
INSERT INTO public.dtb_csv VALUES (146, 4, NULL, 'Eccube\\Entity\\Order', 'Job', 'name', '職業(名称)', 19, true, '2017-03-07 10:14:00+00', '2017-03-07 10:14:00+00', 'csv');
INSERT INTO public.dtb_csv VALUES (147, 4, NULL, 'Eccube\\Entity\\Order', 'birth', NULL, '誕生日', 20, true, '2017-03-07 10:14:00+00', '2017-03-07 10:14:00+00', 'csv');
INSERT INTO public.dtb_csv VALUES (148, 4, NULL, 'Eccube\\Entity\\Order', 'note', NULL, 'ショップ用メモ欄', 21, true, '2017-03-07 10:14:00+00', '2017-03-07 10:14:00+00', 'csv');
INSERT INTO public.dtb_csv VALUES (149, 4, NULL, 'Eccube\\Entity\\Order', 'subtotal', NULL, '小計', 22, true, '2017-03-07 10:14:00+00', '2017-03-07 10:14:00+00', 'csv');
INSERT INTO public.dtb_csv VALUES (150, 4, NULL, 'Eccube\\Entity\\Order', 'discount', NULL, '値引き', 23, true, '2017-03-07 10:14:00+00', '2017-03-07 10:14:00+00', 'csv');
INSERT INTO public.dtb_csv VALUES (151, 4, NULL, 'Eccube\\Entity\\Order', 'delivery_fee_total', NULL, '送料', 24, true, '2017-03-07 10:14:00+00', '2017-03-07 10:14:00+00', 'csv');
INSERT INTO public.dtb_csv VALUES (152, 4, NULL, 'Eccube\\Entity\\Order', 'tax', NULL, '税金', 25, true, '2017-03-07 10:14:00+00', '2017-03-07 10:14:00+00', 'csv');
INSERT INTO public.dtb_csv VALUES (153, 4, NULL, 'Eccube\\Entity\\Order', 'total', NULL, '合計', 26, true, '2017-03-07 10:14:00+00', '2017-03-07 10:14:00+00', 'csv');
INSERT INTO public.dtb_csv VALUES (154, 4, NULL, 'Eccube\\Entity\\Order', 'payment_total', NULL, '支払合計', 27, true, '2017-03-07 10:14:00+00', '2017-03-07 10:14:00+00', 'csv');
INSERT INTO public.dtb_csv VALUES (155, 4, NULL, 'Eccube\\Entity\\Order', 'OrderStatus', 'id', '対応状況(ID)', 28, true, '2017-03-07 10:14:00+00', '2017-03-07 10:14:00+00', 'csv');
INSERT INTO public.dtb_csv VALUES (156, 4, NULL, 'Eccube\\Entity\\Order', 'OrderStatus', 'name', '対応状況(名称)', 29, true, '2017-03-07 10:14:00+00', '2017-03-07 10:14:00+00', 'csv');
INSERT INTO public.dtb_csv VALUES (157, 4, NULL, 'Eccube\\Entity\\Order', 'Payment', 'id', '支払方法(ID)', 30, true, '2017-03-07 10:14:00+00', '2017-03-07 10:14:00+00', 'csv');
INSERT INTO public.dtb_csv VALUES (158, 4, NULL, 'Eccube\\Entity\\Order', 'payment_method', NULL, '支払方法(名称)', 31, true, '2017-03-07 10:14:00+00', '2017-03-07 10:14:00+00', 'csv');
INSERT INTO public.dtb_csv VALUES (159, 4, NULL, 'Eccube\\Entity\\Order', 'order_date', NULL, '受注日', 32, true, '2017-03-07 10:14:00+00', '2017-03-07 10:14:00+00', 'csv');
INSERT INTO public.dtb_csv VALUES (160, 4, NULL, 'Eccube\\Entity\\Order', 'payment_date', NULL, '入金日', 33, true, '2017-03-07 10:14:00+00', '2017-03-07 10:14:00+00', 'csv');
INSERT INTO public.dtb_csv VALUES (161, 4, NULL, 'Eccube\\Entity\\OrderItem', 'id', NULL, '注文詳細ID', 34, true, '2017-03-07 10:14:00+00', '2017-03-07 10:14:00+00', 'csv');
INSERT INTO public.dtb_csv VALUES (162, 4, NULL, 'Eccube\\Entity\\OrderItem', 'Product', 'id', '商品ID', 35, true, '2017-03-07 10:14:00+00', '2017-03-07 10:14:00+00', 'csv');
INSERT INTO public.dtb_csv VALUES (163, 4, NULL, 'Eccube\\Entity\\OrderItem', 'ProductClass', 'id', '商品規格ID', 36, true, '2017-03-07 10:14:00+00', '2017-03-07 10:14:00+00', 'csv');
INSERT INTO public.dtb_csv VALUES (164, 4, NULL, 'Eccube\\Entity\\OrderItem', 'product_name', NULL, '商品名', 37, true, '2017-03-07 10:14:00+00', '2017-03-07 10:14:00+00', 'csv');
INSERT INTO public.dtb_csv VALUES (165, 4, NULL, 'Eccube\\Entity\\OrderItem', 'product_code', NULL, '商品コード', 38, true, '2017-03-07 10:14:00+00', '2017-03-07 10:14:00+00', 'csv');
INSERT INTO public.dtb_csv VALUES (166, 4, NULL, 'Eccube\\Entity\\OrderItem', 'class_name1', NULL, '規格名1', 39, true, '2017-03-07 10:14:00+00', '2017-03-07 10:14:00+00', 'csv');
INSERT INTO public.dtb_csv VALUES (167, 4, NULL, 'Eccube\\Entity\\OrderItem', 'class_name2', NULL, '規格名2', 40, true, '2017-03-07 10:14:00+00', '2017-03-07 10:14:00+00', 'csv');
INSERT INTO public.dtb_csv VALUES (168, 4, NULL, 'Eccube\\Entity\\OrderItem', 'class_category_name1', NULL, '規格分類名1', 41, true, '2017-03-07 10:14:00+00', '2017-03-07 10:14:00+00', 'csv');
INSERT INTO public.dtb_csv VALUES (169, 4, NULL, 'Eccube\\Entity\\OrderItem', 'class_category_name2', NULL, '規格分類名2', 42, true, '2017-03-07 10:14:00+00', '2017-03-07 10:14:00+00', 'csv');
INSERT INTO public.dtb_csv VALUES (170, 4, NULL, 'Eccube\\Entity\\OrderItem', 'price', NULL, '価格', 43, true, '2017-03-07 10:14:00+00', '2017-03-07 10:14:00+00', 'csv');
INSERT INTO public.dtb_csv VALUES (171, 4, NULL, 'Eccube\\Entity\\OrderItem', 'quantity', NULL, '個数', 44, true, '2017-03-07 10:14:00+00', '2017-03-07 10:14:00+00', 'csv');
INSERT INTO public.dtb_csv VALUES (172, 4, NULL, 'Eccube\\Entity\\OrderItem', 'tax_rate', NULL, '税率', 45, true, '2017-03-07 10:14:00+00', '2017-03-07 10:14:00+00', 'csv');
INSERT INTO public.dtb_csv VALUES (173, 4, NULL, 'Eccube\\Entity\\OrderItem', 'tax_rule', NULL, '税率ルール(ID)', 46, true, '2017-03-07 10:14:00+00', '2017-03-07 10:14:00+00', 'csv');
INSERT INTO public.dtb_csv VALUES (174, 4, NULL, 'Eccube\\Entity\\OrderItem', 'OrderItemType', 'id', '明細区分(ID)', 47, true, '2018-07-23 09:00:00+00', '2018-07-23 09:00:00+00', 'csv');
INSERT INTO public.dtb_csv VALUES (175, 4, NULL, 'Eccube\\Entity\\OrderItem', 'OrderItemType', 'name', '明細区分(名称)', 48, true, '2018-07-23 09:00:00+00', '2018-07-23 09:00:00+00', 'csv');
INSERT INTO public.dtb_csv VALUES (176, 4, NULL, 'Eccube\\Entity\\Shipping', 'id', NULL, '出荷ID', 49, true, '2017-03-07 10:14:00+00', '2017-03-07 10:14:00+00', 'csv');
INSERT INTO public.dtb_csv VALUES (177, 4, NULL, 'Eccube\\Entity\\Shipping', 'name01', NULL, '配送先_お名前(姓)', 50, true, '2017-03-07 10:14:00+00', '2017-03-07 10:14:00+00', 'csv');
INSERT INTO public.dtb_csv VALUES (178, 4, NULL, 'Eccube\\Entity\\Shipping', 'name02', NULL, '配送先_お名前(名)', 51, true, '2017-03-07 10:14:00+00', '2017-03-07 10:14:00+00', 'csv');
INSERT INTO public.dtb_csv VALUES (179, 4, NULL, 'Eccube\\Entity\\Shipping', 'kana01', NULL, '配送先_お名前(セイ)', 52, true, '2017-03-07 10:14:00+00', '2017-03-07 10:14:00+00', 'csv');
INSERT INTO public.dtb_csv VALUES (180, 4, NULL, 'Eccube\\Entity\\Shipping', 'kana02', NULL, '配送先_お名前(メイ)', 53, true, '2017-03-07 10:14:00+00', '2017-03-07 10:14:00+00', 'csv');
INSERT INTO public.dtb_csv VALUES (181, 4, NULL, 'Eccube\\Entity\\Shipping', 'company_name', NULL, '配送先_会社名', 54, true, '2017-03-07 10:14:00+00', '2017-03-07 10:14:00+00', 'csv');
INSERT INTO public.dtb_csv VALUES (182, 4, NULL, 'Eccube\\Entity\\Shipping', 'postal_code', NULL, '配送先_郵便番号', 55, true, '2017-03-07 10:14:00+00', '2017-03-07 10:14:00+00', 'csv');
INSERT INTO public.dtb_csv VALUES (183, 4, NULL, 'Eccube\\Entity\\Shipping', 'Pref', 'id', '配送先_都道府県(ID)', 56, true, '2017-03-07 10:14:00+00', '2017-03-07 10:14:00+00', 'csv');
INSERT INTO public.dtb_csv VALUES (184, 4, NULL, 'Eccube\\Entity\\Shipping', 'Pref', 'name', '配送先_都道府県(名称)', 57, true, '2017-03-07 10:14:00+00', '2017-03-07 10:14:00+00', 'csv');
INSERT INTO public.dtb_csv VALUES (185, 4, NULL, 'Eccube\\Entity\\Shipping', 'addr01', NULL, '配送先_住所1', 58, true, '2017-03-07 10:14:00+00', '2017-03-07 10:14:00+00', 'csv');
INSERT INTO public.dtb_csv VALUES (186, 4, NULL, 'Eccube\\Entity\\Shipping', 'addr02', NULL, '配送先_住所2', 59, true, '2017-03-07 10:14:00+00', '2017-03-07 10:14:00+00', 'csv');
INSERT INTO public.dtb_csv VALUES (187, 4, NULL, 'Eccube\\Entity\\Shipping', 'phone_number', NULL, '配送先_TEL', 60, true, '2017-03-07 10:14:00+00', '2017-03-07 10:14:00+00', 'csv');
INSERT INTO public.dtb_csv VALUES (188, 4, NULL, 'Eccube\\Entity\\Shipping', 'Delivery', 'id', '配送業者(ID)', 61, true, '2017-03-07 10:14:00+00', '2017-03-07 10:14:00+00', 'csv');
INSERT INTO public.dtb_csv VALUES (189, 4, NULL, 'Eccube\\Entity\\Shipping', 'shipping_delivery_name', NULL, '配送業者(名称)', 62, true, '2017-03-07 10:14:00+00', '2017-03-07 10:14:00+00', 'csv');
INSERT INTO public.dtb_csv VALUES (190, 4, NULL, 'Eccube\\Entity\\Shipping', 'time_id', NULL, 'お届け時間ID', 63, true, '2017-03-07 10:14:00+00', '2017-03-07 10:14:00+00', 'csv');
INSERT INTO public.dtb_csv VALUES (191, 4, NULL, 'Eccube\\Entity\\Shipping', 'shipping_delivery_time', NULL, 'お届け時間(名称)', 64, true, '2017-03-07 10:14:00+00', '2017-03-07 10:14:00+00', 'csv');
INSERT INTO public.dtb_csv VALUES (192, 4, NULL, 'Eccube\\Entity\\Shipping', 'shipping_delivery_date', NULL, 'お届け希望日', 65, true, '2017-03-07 10:14:00+00', '2017-03-07 10:14:00+00', 'csv');
INSERT INTO public.dtb_csv VALUES (194, 4, NULL, 'Eccube\\Entity\\Shipping', 'shipping_delivery_fee', NULL, '送料', 67, true, '2017-03-07 10:14:00+00', '2017-03-07 10:14:00+00', 'csv');
INSERT INTO public.dtb_csv VALUES (195, 4, NULL, 'Eccube\\Entity\\Shipping', 'shipping_date', NULL, '発送日', 68, true, '2017-03-07 10:14:00+00', '2017-03-07 10:14:00+00', 'csv');
INSERT INTO public.dtb_csv VALUES (196, 4, NULL, 'Eccube\\Entity\\Shipping', 'tracking_number', NULL, '出荷伝票番号', 69, true, '2017-03-07 10:14:00+00', '2017-03-07 10:14:00+00', 'csv');
INSERT INTO public.dtb_csv VALUES (197, 4, NULL, 'Eccube\\Entity\\Shipping', 'note', NULL, '配達用メモ', 70, true, '2017-03-07 10:14:00+00', '2017-03-07 10:14:00+00', 'csv');
INSERT INTO public.dtb_csv VALUES (198, 4, NULL, 'Eccube\\Entity\\Shipping', 'mail_send_date', NULL, '出荷メール送信日', 71, true, '2017-03-07 10:14:00+00', '2017-03-07 10:14:00+00', 'csv');
INSERT INTO public.dtb_csv VALUES (199, 5, NULL, 'Eccube\\Entity\\Category', 'id', NULL, 'カテゴリID', 1, true, '2017-03-07 10:14:00+00', '2017-03-07 10:14:00+00', 'csv');
INSERT INTO public.dtb_csv VALUES (200, 5, NULL, 'Eccube\\Entity\\Category', 'sort_no', NULL, '表示ランク', 2, true, '2017-03-07 10:14:00+00', '2017-03-07 10:14:00+00', 'csv');
INSERT INTO public.dtb_csv VALUES (201, 5, NULL, 'Eccube\\Entity\\Category', 'name', NULL, 'カテゴリ名', 3, true, '2017-03-07 10:14:00+00', '2017-03-07 10:14:00+00', 'csv');
INSERT INTO public.dtb_csv VALUES (202, 5, NULL, 'Eccube\\Entity\\Category', 'Parent', 'id', '親カテゴリID', 4, true, '2017-03-07 10:14:00+00', '2017-03-07 10:14:00+00', 'csv');
INSERT INTO public.dtb_csv VALUES (203, 5, NULL, 'Eccube\\Entity\\Category', 'level', NULL, '階層', 5, true, '2017-03-07 10:14:00+00', '2017-03-07 10:14:00+00', 'csv');
INSERT INTO public.dtb_csv VALUES (204, 1, NULL, 'Eccube\\Entity\\ProductClass', 'TaxRule', 'tax_rate', '税率', 31, false, '2017-03-07 10:14:00+00', '2017-03-07 10:14:00+00', 'csv');
INSERT INTO public.dtb_csv VALUES (205, 2, NULL, 'Eccube\\Entity\\Customer', 'point', NULL, 'ポイント', 33, true, '2017-03-07 10:14:00+00', '2017-03-07 10:14:00+00', 'csv');


--
-- Data for Name: dtb_customer; Type: TABLE DATA; Schema: public; Owner: dbuser
--

INSERT INTO public.dtb_customer VALUES (1, 2, 1, 11, NULL, 11, '吉本', '明美', 'ナギサ', 'リョウヘイ', '有限会社 渚', '8955032', '宮沢市', '井上町鈴木6-6-4', '1656574314.8323.ntanaka@example.org', '09051823959', '1972-05-23 20:51:17+00', 'c4287af5f432b0ad366afa5079de9788453e1fbf26204f72059991b3c88e0a96', 'ba86ca061d', 'gAnib06HNONh2uK7pHbpW0kDERXlRBOF', NULL, NULL, 0, 0.00, NULL, NULL, NULL, 16883, '2022-06-30 07:31:54+00', '2022-06-30 07:31:54+00', 'customer');


--
-- Data for Name: dtb_customer_address; Type: TABLE DATA; Schema: public; Owner: dbuser
--



--
-- Data for Name: dtb_customer_favorite_product; Type: TABLE DATA; Schema: public; Owner: dbuser
--



--
-- Data for Name: dtb_delivery; Type: TABLE DATA; Schema: public; Owner: dbuser
--

INSERT INTO public.dtb_delivery VALUES (1, NULL, 1, 'サンプル業者', 'サンプル業者', NULL, NULL, 1, true, '2017-03-07 10:14:52+00', '2017-03-07 10:14:52+00', 'delivery');
INSERT INTO public.dtb_delivery VALUES (2, NULL, 2, 'サンプル宅配', 'サンプル宅配', NULL, NULL, 2, true, '2017-03-07 10:14:52+00', '2017-03-07 10:14:52+00', 'delivery');


--
-- Data for Name: dtb_delivery_duration; Type: TABLE DATA; Schema: public; Owner: dbuser
--

INSERT INTO public.dtb_delivery_duration VALUES (1, '即日', 0, 0, 'deliveryduration');
INSERT INTO public.dtb_delivery_duration VALUES (2, '1～2日後', 1, 1, 'deliveryduration');
INSERT INTO public.dtb_delivery_duration VALUES (3, '3～4日後', 3, 2, 'deliveryduration');
INSERT INTO public.dtb_delivery_duration VALUES (4, '1週間以降', 7, 3, 'deliveryduration');
INSERT INTO public.dtb_delivery_duration VALUES (5, '2週間以降', 14, 4, 'deliveryduration');
INSERT INTO public.dtb_delivery_duration VALUES (6, '3週間以降', 21, 5, 'deliveryduration');
INSERT INTO public.dtb_delivery_duration VALUES (7, '1ヶ月以降', 30, 6, 'deliveryduration');
INSERT INTO public.dtb_delivery_duration VALUES (8, '2ヶ月以降', 60, 7, 'deliveryduration');
INSERT INTO public.dtb_delivery_duration VALUES (9, 'お取り寄せ(商品入荷後)', -1, 8, 'deliveryduration');


--
-- Data for Name: dtb_delivery_fee; Type: TABLE DATA; Schema: public; Owner: dbuser
--

INSERT INTO public.dtb_delivery_fee VALUES (1, 1, 1, 1000.00, 'deliveryfee');
INSERT INTO public.dtb_delivery_fee VALUES (2, 1, 2, 1000.00, 'deliveryfee');
INSERT INTO public.dtb_delivery_fee VALUES (3, 1, 3, 1000.00, 'deliveryfee');
INSERT INTO public.dtb_delivery_fee VALUES (4, 1, 4, 1000.00, 'deliveryfee');
INSERT INTO public.dtb_delivery_fee VALUES (5, 1, 5, 1000.00, 'deliveryfee');
INSERT INTO public.dtb_delivery_fee VALUES (6, 1, 6, 1000.00, 'deliveryfee');
INSERT INTO public.dtb_delivery_fee VALUES (7, 1, 7, 1000.00, 'deliveryfee');
INSERT INTO public.dtb_delivery_fee VALUES (8, 1, 8, 1000.00, 'deliveryfee');
INSERT INTO public.dtb_delivery_fee VALUES (9, 1, 9, 1000.00, 'deliveryfee');
INSERT INTO public.dtb_delivery_fee VALUES (10, 1, 10, 1000.00, 'deliveryfee');
INSERT INTO public.dtb_delivery_fee VALUES (11, 1, 11, 1000.00, 'deliveryfee');
INSERT INTO public.dtb_delivery_fee VALUES (12, 1, 12, 1000.00, 'deliveryfee');
INSERT INTO public.dtb_delivery_fee VALUES (13, 1, 13, 1000.00, 'deliveryfee');
INSERT INTO public.dtb_delivery_fee VALUES (14, 1, 14, 1000.00, 'deliveryfee');
INSERT INTO public.dtb_delivery_fee VALUES (15, 1, 15, 1000.00, 'deliveryfee');
INSERT INTO public.dtb_delivery_fee VALUES (16, 1, 16, 1000.00, 'deliveryfee');
INSERT INTO public.dtb_delivery_fee VALUES (17, 1, 17, 1000.00, 'deliveryfee');
INSERT INTO public.dtb_delivery_fee VALUES (18, 1, 18, 1000.00, 'deliveryfee');
INSERT INTO public.dtb_delivery_fee VALUES (19, 1, 19, 1000.00, 'deliveryfee');
INSERT INTO public.dtb_delivery_fee VALUES (20, 1, 20, 1000.00, 'deliveryfee');
INSERT INTO public.dtb_delivery_fee VALUES (21, 1, 21, 1000.00, 'deliveryfee');
INSERT INTO public.dtb_delivery_fee VALUES (22, 1, 22, 1000.00, 'deliveryfee');
INSERT INTO public.dtb_delivery_fee VALUES (23, 1, 23, 1000.00, 'deliveryfee');
INSERT INTO public.dtb_delivery_fee VALUES (24, 1, 24, 1000.00, 'deliveryfee');
INSERT INTO public.dtb_delivery_fee VALUES (25, 1, 25, 1000.00, 'deliveryfee');
INSERT INTO public.dtb_delivery_fee VALUES (26, 1, 26, 1000.00, 'deliveryfee');
INSERT INTO public.dtb_delivery_fee VALUES (27, 1, 27, 1000.00, 'deliveryfee');
INSERT INTO public.dtb_delivery_fee VALUES (28, 1, 28, 1000.00, 'deliveryfee');
INSERT INTO public.dtb_delivery_fee VALUES (29, 1, 29, 1000.00, 'deliveryfee');
INSERT INTO public.dtb_delivery_fee VALUES (30, 1, 30, 1000.00, 'deliveryfee');
INSERT INTO public.dtb_delivery_fee VALUES (31, 1, 31, 1000.00, 'deliveryfee');
INSERT INTO public.dtb_delivery_fee VALUES (32, 1, 32, 1000.00, 'deliveryfee');
INSERT INTO public.dtb_delivery_fee VALUES (33, 1, 33, 1000.00, 'deliveryfee');
INSERT INTO public.dtb_delivery_fee VALUES (34, 1, 34, 1000.00, 'deliveryfee');
INSERT INTO public.dtb_delivery_fee VALUES (35, 1, 35, 1000.00, 'deliveryfee');
INSERT INTO public.dtb_delivery_fee VALUES (36, 1, 36, 1000.00, 'deliveryfee');
INSERT INTO public.dtb_delivery_fee VALUES (37, 1, 37, 1000.00, 'deliveryfee');
INSERT INTO public.dtb_delivery_fee VALUES (38, 1, 38, 1000.00, 'deliveryfee');
INSERT INTO public.dtb_delivery_fee VALUES (39, 1, 39, 1000.00, 'deliveryfee');
INSERT INTO public.dtb_delivery_fee VALUES (40, 1, 40, 1000.00, 'deliveryfee');
INSERT INTO public.dtb_delivery_fee VALUES (41, 1, 41, 1000.00, 'deliveryfee');
INSERT INTO public.dtb_delivery_fee VALUES (42, 1, 42, 1000.00, 'deliveryfee');
INSERT INTO public.dtb_delivery_fee VALUES (43, 1, 43, 1000.00, 'deliveryfee');
INSERT INTO public.dtb_delivery_fee VALUES (44, 1, 44, 1000.00, 'deliveryfee');
INSERT INTO public.dtb_delivery_fee VALUES (45, 1, 45, 1000.00, 'deliveryfee');
INSERT INTO public.dtb_delivery_fee VALUES (46, 1, 46, 1000.00, 'deliveryfee');
INSERT INTO public.dtb_delivery_fee VALUES (47, 1, 47, 1000.00, 'deliveryfee');
INSERT INTO public.dtb_delivery_fee VALUES (48, 2, 1, 0.00, 'deliveryfee');
INSERT INTO public.dtb_delivery_fee VALUES (49, 2, 2, 0.00, 'deliveryfee');
INSERT INTO public.dtb_delivery_fee VALUES (50, 2, 3, 0.00, 'deliveryfee');
INSERT INTO public.dtb_delivery_fee VALUES (51, 2, 4, 0.00, 'deliveryfee');
INSERT INTO public.dtb_delivery_fee VALUES (52, 2, 5, 0.00, 'deliveryfee');
INSERT INTO public.dtb_delivery_fee VALUES (53, 2, 6, 0.00, 'deliveryfee');
INSERT INTO public.dtb_delivery_fee VALUES (54, 2, 7, 0.00, 'deliveryfee');
INSERT INTO public.dtb_delivery_fee VALUES (55, 2, 8, 0.00, 'deliveryfee');
INSERT INTO public.dtb_delivery_fee VALUES (56, 2, 9, 0.00, 'deliveryfee');
INSERT INTO public.dtb_delivery_fee VALUES (57, 2, 10, 0.00, 'deliveryfee');
INSERT INTO public.dtb_delivery_fee VALUES (58, 2, 11, 0.00, 'deliveryfee');
INSERT INTO public.dtb_delivery_fee VALUES (59, 2, 12, 0.00, 'deliveryfee');
INSERT INTO public.dtb_delivery_fee VALUES (60, 2, 13, 0.00, 'deliveryfee');
INSERT INTO public.dtb_delivery_fee VALUES (61, 2, 14, 0.00, 'deliveryfee');
INSERT INTO public.dtb_delivery_fee VALUES (62, 2, 15, 0.00, 'deliveryfee');
INSERT INTO public.dtb_delivery_fee VALUES (63, 2, 16, 0.00, 'deliveryfee');
INSERT INTO public.dtb_delivery_fee VALUES (64, 2, 17, 0.00, 'deliveryfee');
INSERT INTO public.dtb_delivery_fee VALUES (65, 2, 18, 0.00, 'deliveryfee');
INSERT INTO public.dtb_delivery_fee VALUES (66, 2, 19, 0.00, 'deliveryfee');
INSERT INTO public.dtb_delivery_fee VALUES (67, 2, 20, 0.00, 'deliveryfee');
INSERT INTO public.dtb_delivery_fee VALUES (68, 2, 21, 0.00, 'deliveryfee');
INSERT INTO public.dtb_delivery_fee VALUES (69, 2, 22, 0.00, 'deliveryfee');
INSERT INTO public.dtb_delivery_fee VALUES (70, 2, 23, 0.00, 'deliveryfee');
INSERT INTO public.dtb_delivery_fee VALUES (71, 2, 24, 0.00, 'deliveryfee');
INSERT INTO public.dtb_delivery_fee VALUES (72, 2, 25, 0.00, 'deliveryfee');
INSERT INTO public.dtb_delivery_fee VALUES (73, 2, 26, 0.00, 'deliveryfee');
INSERT INTO public.dtb_delivery_fee VALUES (74, 2, 27, 0.00, 'deliveryfee');
INSERT INTO public.dtb_delivery_fee VALUES (75, 2, 28, 0.00, 'deliveryfee');
INSERT INTO public.dtb_delivery_fee VALUES (76, 2, 29, 0.00, 'deliveryfee');
INSERT INTO public.dtb_delivery_fee VALUES (77, 2, 30, 0.00, 'deliveryfee');
INSERT INTO public.dtb_delivery_fee VALUES (78, 2, 31, 0.00, 'deliveryfee');
INSERT INTO public.dtb_delivery_fee VALUES (79, 2, 32, 0.00, 'deliveryfee');
INSERT INTO public.dtb_delivery_fee VALUES (80, 2, 33, 0.00, 'deliveryfee');
INSERT INTO public.dtb_delivery_fee VALUES (81, 2, 34, 0.00, 'deliveryfee');
INSERT INTO public.dtb_delivery_fee VALUES (82, 2, 35, 0.00, 'deliveryfee');
INSERT INTO public.dtb_delivery_fee VALUES (83, 2, 36, 0.00, 'deliveryfee');
INSERT INTO public.dtb_delivery_fee VALUES (84, 2, 37, 0.00, 'deliveryfee');
INSERT INTO public.dtb_delivery_fee VALUES (85, 2, 38, 0.00, 'deliveryfee');
INSERT INTO public.dtb_delivery_fee VALUES (86, 2, 39, 0.00, 'deliveryfee');
INSERT INTO public.dtb_delivery_fee VALUES (87, 2, 40, 0.00, 'deliveryfee');
INSERT INTO public.dtb_delivery_fee VALUES (88, 2, 41, 0.00, 'deliveryfee');
INSERT INTO public.dtb_delivery_fee VALUES (89, 2, 42, 0.00, 'deliveryfee');
INSERT INTO public.dtb_delivery_fee VALUES (90, 2, 43, 0.00, 'deliveryfee');
INSERT INTO public.dtb_delivery_fee VALUES (91, 2, 44, 0.00, 'deliveryfee');
INSERT INTO public.dtb_delivery_fee VALUES (92, 2, 45, 0.00, 'deliveryfee');
INSERT INTO public.dtb_delivery_fee VALUES (93, 2, 46, 0.00, 'deliveryfee');
INSERT INTO public.dtb_delivery_fee VALUES (94, 2, 47, 0.00, 'deliveryfee');


--
-- Data for Name: dtb_delivery_time; Type: TABLE DATA; Schema: public; Owner: dbuser
--

INSERT INTO public.dtb_delivery_time VALUES (1, 1, '午前', 1, true, '2017-03-07 10:14:52+00', '2017-03-07 10:14:52+00', 'deliverytime');
INSERT INTO public.dtb_delivery_time VALUES (2, 1, '午後', 2, true, '2017-03-07 10:14:52+00', '2017-03-07 10:14:52+00', 'deliverytime');


--
-- Data for Name: dtb_layout; Type: TABLE DATA; Schema: public; Owner: dbuser
--

INSERT INTO public.dtb_layout VALUES (0, 10, 'プレビュー用レイアウト', '2017-03-07 10:14:52+00', '2017-03-07 10:14:52+00', 'layout');
INSERT INTO public.dtb_layout VALUES (1, 10, 'トップページ用レイアウト', '2017-03-07 10:14:52+00', '2017-03-07 10:14:52+00', 'layout');
INSERT INTO public.dtb_layout VALUES (2, 10, '下層ページ用レイアウト', '2017-03-07 10:14:52+00', '2017-03-07 10:14:52+00', 'layout');


--
-- Data for Name: dtb_login_history; Type: TABLE DATA; Schema: public; Owner: dbuser
--



--
-- Data for Name: dtb_mail_history; Type: TABLE DATA; Schema: public; Owner: dbuser
--



--
-- Data for Name: dtb_mail_template; Type: TABLE DATA; Schema: public; Owner: dbuser
--

INSERT INTO public.dtb_mail_template VALUES (1, NULL, '注文受付メール', 'Mail/order.twig', 'ご注文ありがとうございます', '2017-03-07 10:14:52+00', '2017-03-07 10:14:52+00', 'mailtemplate');
INSERT INTO public.dtb_mail_template VALUES (2, NULL, '会員仮登録メール', 'Mail/entry_confirm.twig', '会員登録のご確認', '2017-03-07 10:14:52+00', '2017-03-07 10:14:52+00', 'mailtemplate');
INSERT INTO public.dtb_mail_template VALUES (3, NULL, '会員本登録メール', 'Mail/entry_complete.twig', '会員登録が完了しました。', '2017-03-07 10:14:52+00', '2017-03-07 10:14:52+00', 'mailtemplate');
INSERT INTO public.dtb_mail_template VALUES (4, NULL, '会員退会メール', 'Mail/customer_withdraw_mail.twig', '退会手続きのご完了', '2017-03-07 10:14:52+00', '2017-03-07 10:14:52+00', 'mailtemplate');
INSERT INTO public.dtb_mail_template VALUES (5, NULL, '問合受付メール', 'Mail/contact_mail.twig', 'お問い合わせを受け付けました。', '2017-03-07 10:14:52+00', '2017-03-07 10:14:52+00', 'mailtemplate');
INSERT INTO public.dtb_mail_template VALUES (6, NULL, 'パスワードリセット', 'Mail/forgot_mail.twig', 'パスワード変更のご確認', '2017-03-07 10:14:52+00', '2017-03-07 10:14:52+00', 'mailtemplate');
INSERT INTO public.dtb_mail_template VALUES (7, NULL, 'パスワードリマインダー', 'Mail/reset_complete_mail.twig', 'パスワード変更のお知らせ', '2017-03-07 10:14:52+00', '2017-03-07 10:14:52+00', 'mailtemplate');
INSERT INTO public.dtb_mail_template VALUES (8, NULL, '出荷通知メール', 'Mail/shipping_notify.twig', '商品出荷のお知らせ', '2017-03-07 10:14:52+00', '2017-03-07 10:14:52+00', 'mailtemplate');


--
-- Data for Name: dtb_member; Type: TABLE DATA; Schema: public; Owner: dbuser
--

INSERT INTO public.dtb_member VALUES (1, 1, 0, 1, '管理者', 'EC-CUBE SHOP', 'admin', '6e1d066ac2d0768d8646e539db8338282caa362614e5201a9fbb5ebe0307bc5f', 'K7O0oQ8293VgOO35JK4zQs1rSceZ3kTd', 1, NULL, false, '2022-06-30 07:31:44+00', '2022-06-30 07:31:44+00', NULL, 'member');


--
-- Data for Name: dtb_news; Type: TABLE DATA; Schema: public; Owner: dbuser
--

INSERT INTO public.dtb_news VALUES (1, NULL, '2018-09-01 09:00:00+00', 'サイトオープンいたしました!', '旬の色どりスイーツとこだわりのジェラートをお届けします。', NULL, true, '2018-09-01 09:00:00+00', '2018-09-01 09:00:00+00', true, 'news');


--
-- Data for Name: dtb_order; Type: TABLE DATA; Schema: public; Owner: dbuser
--

INSERT INTO public.dtb_order VALUES (1, 1, NULL, 7, 1, 11, 2, NULL, '48e352e2dbeb530e9e02452e891f5854d0681597', '222-9087454-7815540', '町うらしているのです」「あらわしそうについ硝子ガラスの木という声やらでも行ける切符きっぷだ。さぎの木がほとんどうしたら、みんなほんとつレンズを指さしていま新しく、あの夏じゅうの窓まどは一時間になっていたのでした。二人ふたり鳥へ信号手しんに丘おかしやだわねえさんは、茶いろにみんなそういちどまっすぐそこか苦くるくるし僕ぼく飛とび出してもいつるした。車室に、何べんも眼めを大股おおきく天井てんきょうは紙。', '吉本', '明美', 'ナギサ', 'リョウヘイ', '有限会社 渚', '1656574314.8323.ntanaka@example.org', '09051823959', '8955032', '宮沢市', '井上町鈴木6-6-4', '1972-05-23 20:51:17+00', 20633760.00, 9492.00, 0.00, 4719.00, 1876233.00, 20628987.00, 20628987.00, '現金書留', 'っちでも私の手首てくるとジョバンニが町を通りにした。その笑わらかの光る火はちょうがかった」ごとにほんとうとうとしてその鳥捕とりは顔をした。けれどもありました。「ほんとも、誰だれだけは浮うかんごがそっちに五つの街燈がいとう、凍こおり、どこかに流ながらんだ。いけむるように。どん小さくなって歴史れきしとならないたジョバンニが言いいちょうざんでなけぁいけない。まっすぐ近くのお星さまよ」その子の手をのばし。', '2022-06-30 07:32:33+00', '2022-06-30 07:32:33+00', '2021-12-11 18:26:30+00', NULL, 'JPY', NULL, NULL, 187561, 0, 7, 'order');


--
-- Data for Name: dtb_order_item; Type: TABLE DATA; Schema: public; Owner: dbuser
--

INSERT INTO public.dtb_order_item VALUES (1, 1, 90, 360, 1, 1, 1, 1, 1, 'エルというちももってうなさんに、ほんとう。', 'est', 'フレーバー', 'サイズ', 'チョコ', '16mm × 16mm', 86164.00, 84, 8616, 10, 0, NULL, 'JPY', NULL, NULL, 'orderitem');
INSERT INTO public.dtb_order_item VALUES (2, 1, 90, 361, 1, 1, 1, 1, 1, 'エルというちももってうなさんに、ほんとう。', 'expedita', 'フレーバー', 'サイズ', '抹茶', '32mm × 32mm', 47978.00, 84, 4798, 10, 0, NULL, 'JPY', NULL, NULL, 'orderitem');
INSERT INTO public.dtb_order_item VALUES (3, 1, 90, 362, 1, 1, 1, 1, 1, 'エルというちももってうなさんに、ほんとう。', 'nesciunt', 'フレーバー', 'サイズ', 'バニラ', '64cm × 64cm', 89167.00, 84, 8917, 10, 0, NULL, 'JPY', NULL, NULL, 'orderitem');
INSERT INTO public.dtb_order_item VALUES (4, 1, NULL, NULL, 1, 1, 1, 2, 2, '送料', NULL, NULL, NULL, NULL, NULL, 0.00, 1, 0, 10, 0, NULL, 'JPY', NULL, NULL, 'orderitem');
INSERT INTO public.dtb_order_item VALUES (5, 1, NULL, NULL, NULL, 1, 1, 2, 3, '手数料', NULL, NULL, NULL, NULL, NULL, 4719.00, 1, 429, 10, 0, NULL, 'JPY', NULL, NULL, 'orderitem');
INSERT INTO public.dtb_order_item VALUES (6, 1, NULL, NULL, NULL, NULL, 2, 2, 4, '値引き', NULL, NULL, NULL, NULL, NULL, -9492.00, 1, 0, 0, 0, NULL, 'JPY', NULL, NULL, 'orderitem');


--
-- Data for Name: dtb_order_pdf; Type: TABLE DATA; Schema: public; Owner: dbuser
--



--
-- Data for Name: dtb_page; Type: TABLE DATA; Schema: public; Owner: dbuser
--

INSERT INTO public.dtb_page VALUES (0, NULL, 'プレビューデータ', 'preview', NULL, 1, NULL, NULL, NULL, '2017-03-07 10:14:52+00', '2017-03-07 10:14:52+00', NULL, NULL, 'page');
INSERT INTO public.dtb_page VALUES (1, NULL, 'TOPページ', 'homepage', 'index', 2, NULL, NULL, NULL, '2017-03-07 10:14:52+00', '2017-03-07 10:14:52+00', NULL, NULL, 'page');
INSERT INTO public.dtb_page VALUES (2, NULL, '商品一覧ページ', 'product_list', 'Product/list', 2, NULL, NULL, NULL, '2017-03-07 10:14:52+00', '2017-03-07 10:14:52+00', NULL, NULL, 'page');
INSERT INTO public.dtb_page VALUES (3, NULL, '商品詳細ページ', 'product_detail', 'Product/detail', 2, NULL, NULL, NULL, '2017-03-07 10:14:52+00', '2017-03-07 10:14:52+00', NULL, NULL, 'page');
INSERT INTO public.dtb_page VALUES (4, NULL, 'MYページ', 'mypage', 'Mypage/index', 2, NULL, NULL, NULL, '2017-03-07 10:14:52+00', '2017-03-07 10:14:52+00', 'noindex', NULL, 'page');
INSERT INTO public.dtb_page VALUES (5, NULL, 'MYページ/会員登録内容変更(入力ページ)', 'mypage_change', 'Mypage/change', 2, NULL, NULL, NULL, '2017-03-07 10:14:52+00', '2017-03-07 10:14:52+00', 'noindex', NULL, 'page');
INSERT INTO public.dtb_page VALUES (6, NULL, 'MYページ/会員登録内容変更(完了ページ)', 'mypage_change_complete', 'Mypage/change_complete', 2, NULL, NULL, NULL, '2017-03-07 10:14:52+00', '2017-03-07 10:14:52+00', 'noindex', NULL, 'page');
INSERT INTO public.dtb_page VALUES (9, NULL, 'MYページ/お気に入り一覧', 'mypage_favorite', 'Mypage/favorite', 2, NULL, NULL, NULL, '2017-03-07 10:14:52+00', '2017-03-07 10:14:52+00', 'noindex', NULL, 'page');
INSERT INTO public.dtb_page VALUES (10, NULL, 'MYページ/購入履歴詳細', 'mypage_history', 'Mypage/history', 2, NULL, NULL, NULL, '2017-03-07 10:14:52+00', '2017-03-07 10:14:52+00', 'noindex', NULL, 'page');
INSERT INTO public.dtb_page VALUES (11, NULL, 'MYページ/ログイン', 'mypage_login', 'Mypage/login', 2, NULL, NULL, NULL, '2017-03-07 10:14:52+00', '2017-03-07 10:14:52+00', 'noindex', NULL, 'page');
INSERT INTO public.dtb_page VALUES (12, NULL, 'MYページ/退会手続き(入力ページ)', 'mypage_withdraw', 'Mypage/withdraw', 2, NULL, NULL, NULL, '2017-03-07 10:14:52+00', '2017-03-07 10:14:52+00', 'noindex', NULL, 'page');
INSERT INTO public.dtb_page VALUES (14, NULL, '当サイトについて', 'help_about', 'Help/about', 2, NULL, NULL, NULL, '2017-03-07 10:14:52+00', '2017-03-07 10:14:52+00', NULL, NULL, 'page');
INSERT INTO public.dtb_page VALUES (13, NULL, 'MYページ/退会手続き(完了ページ)', 'mypage_withdraw_complete', 'Mypage/withdraw_complete', 2, NULL, NULL, NULL, '2017-03-07 10:14:52+00', '2017-03-07 10:14:52+00', 'noindex', NULL, 'page');
INSERT INTO public.dtb_page VALUES (15, NULL, '現在のカゴの中', 'cart', 'Cart/index', 2, NULL, NULL, NULL, '2017-03-07 10:14:52+00', '2017-03-07 10:14:52+00', 'noindex', NULL, 'page');
INSERT INTO public.dtb_page VALUES (16, NULL, 'お問い合わせ(入力ページ)', 'contact', 'Contact/index', 2, NULL, NULL, NULL, '2017-03-07 10:14:52+00', '2017-03-07 10:14:52+00', NULL, NULL, 'page');
INSERT INTO public.dtb_page VALUES (17, NULL, 'お問い合わせ(完了ページ)', 'contact_complete', 'Contact/complete', 2, NULL, NULL, NULL, '2017-03-07 10:14:52+00', '2017-03-07 10:14:52+00', 'noindex', NULL, 'page');
INSERT INTO public.dtb_page VALUES (18, NULL, '会員登録(入力ページ)', 'entry', 'Entry/index', 2, NULL, NULL, NULL, '2017-03-07 10:14:52+00', '2017-03-07 10:14:52+00', NULL, NULL, 'page');
INSERT INTO public.dtb_page VALUES (20, NULL, '会員登録(完了ページ)', 'entry_complete', 'Entry/complete', 2, NULL, NULL, NULL, '2017-03-07 10:14:52+00', '2017-03-07 10:14:52+00', 'noindex', NULL, 'page');
INSERT INTO public.dtb_page VALUES (21, NULL, '特定商取引に関する法律に基づく表記', 'help_tradelaw', 'Help/tradelaw', 2, NULL, NULL, NULL, '2017-03-07 10:14:52+00', '2017-03-07 10:14:52+00', NULL, NULL, 'page');
INSERT INTO public.dtb_page VALUES (22, NULL, '本会員登録(完了ページ)', 'entry_activate', 'Entry/activate', 2, NULL, NULL, NULL, '2017-03-07 10:14:52+00', '2017-03-07 10:14:52+00', 'noindex', NULL, 'page');
INSERT INTO public.dtb_page VALUES (24, NULL, '商品購入/お届け先の指定', 'shopping_shipping', 'Shopping/shipping', 2, NULL, NULL, NULL, '2017-03-07 10:14:52+00', '2017-03-07 10:14:52+00', 'noindex', NULL, 'page');
INSERT INTO public.dtb_page VALUES (28, NULL, '商品購入/ご注文完了', 'shopping_complete', 'Shopping/complete', 2, NULL, NULL, NULL, '2017-03-07 10:14:52+00', '2017-03-07 10:14:52+00', 'noindex', NULL, 'page');
INSERT INTO public.dtb_page VALUES (29, NULL, 'プライバシーポリシー', 'help_privacy', 'Help/privacy', 2, NULL, NULL, NULL, '2017-03-07 10:14:52+00', '2017-03-07 10:14:52+00', NULL, NULL, 'page');
INSERT INTO public.dtb_page VALUES (30, NULL, '商品購入ログイン', 'shopping_login', 'Shopping/login', 2, NULL, NULL, NULL, '2017-03-07 10:14:52+00', '2017-03-07 10:14:52+00', 'noindex', NULL, 'page');
INSERT INTO public.dtb_page VALUES (31, NULL, '非会員購入情報入力', 'shopping_nonmember', 'Shopping/nonmember', 2, NULL, NULL, NULL, '2017-03-07 10:14:52+00', '2017-03-07 10:14:52+00', 'noindex', NULL, 'page');
INSERT INTO public.dtb_page VALUES (32, NULL, '商品購入/お届け先の追加', 'shopping_shipping_edit', 'Shopping/shipping_edit', 2, NULL, NULL, NULL, '2017-03-07 01:15:02+00', '2017-03-07 01:15:02+00', 'noindex', NULL, 'page');
INSERT INTO public.dtb_page VALUES (33, NULL, '商品購入/お届け先の複数指定(お届け先の追加)', 'shopping_shipping_multiple_edit', 'Shopping/shipping_multiple_edit', 2, NULL, NULL, NULL, '2017-03-07 01:15:02+00', '2017-03-07 01:15:02+00', 'noindex', NULL, 'page');
INSERT INTO public.dtb_page VALUES (34, NULL, '商品購入/購入エラー', 'shopping_error', 'Shopping/shopping_error', 2, NULL, NULL, NULL, '2017-03-07 01:15:02+00', '2017-03-07 01:15:02+00', 'noindex', NULL, 'page');
INSERT INTO public.dtb_page VALUES (35, NULL, 'ご利用ガイド', 'help_guide', 'Help/guide', 2, NULL, NULL, NULL, '2017-03-07 01:15:02+00', '2017-03-07 01:15:02+00', NULL, NULL, 'page');
INSERT INTO public.dtb_page VALUES (36, NULL, 'パスワード再発行(入力ページ)', 'forgot', 'Forgot/index', 2, NULL, NULL, NULL, '2017-03-07 01:15:02+00', '2017-03-07 01:15:02+00', NULL, NULL, 'page');
INSERT INTO public.dtb_page VALUES (37, NULL, 'パスワード再発行(完了ページ)', 'forgot_complete', 'Forgot/complete', 2, NULL, NULL, NULL, '2017-03-07 01:15:02+00', '2017-03-07 01:15:02+00', 'noindex', NULL, 'page');
INSERT INTO public.dtb_page VALUES (38, NULL, 'パスワード再発行(再設定ページ)', 'forgot_reset', 'Forgot/reset', 2, NULL, NULL, NULL, '2017-03-07 01:15:02+00', '2017-03-07 01:15:05+00', 'noindex', NULL, 'page');
INSERT INTO public.dtb_page VALUES (19, NULL, 'ご利用規約', 'help_agreement', 'Help/agreement', 2, NULL, NULL, NULL, '2017-03-07 10:14:52+00', '2017-03-07 10:14:52+00', NULL, NULL, 'page');
INSERT INTO public.dtb_page VALUES (25, NULL, '商品購入/お届け先の複数指定', 'shopping_shipping_multiple', 'Shopping/shipping_multiple', 2, NULL, NULL, NULL, '2017-03-07 10:14:52+00', '2017-03-07 10:14:52+00', 'noindex', NULL, 'page');
INSERT INTO public.dtb_page VALUES (23, NULL, '商品購入', 'shopping', 'Shopping/index', 2, NULL, NULL, NULL, '2017-03-07 10:14:52+00', '2017-03-07 10:14:52+00', 'noindex', NULL, 'page');
INSERT INTO public.dtb_page VALUES (7, NULL, 'MYページ/お届け先一覧', 'mypage_delivery', 'Mypage/delivery', 2, NULL, NULL, NULL, '2017-03-07 10:14:52+00', '2017-03-07 10:14:52+00', 'noindex', NULL, 'page');
INSERT INTO public.dtb_page VALUES (8, NULL, 'MYページ/お届け先追加', 'mypage_delivery_new', 'Mypage/delivery_edit', 2, NULL, NULL, NULL, '2017-03-07 10:14:52+00', '2017-03-07 10:14:52+00', 'noindex', NULL, 'page');
INSERT INTO public.dtb_page VALUES (42, NULL, '商品購入/遷移', 'shopping_redirect_to', 'Shopping/index', 2, NULL, NULL, NULL, '2017-03-07 01:15:03+00', '2017-03-07 01:15:03+00', 'noindex', NULL, 'page');
INSERT INTO public.dtb_page VALUES (44, 8, 'MYページ/お届け先編集', 'mypage_delivery_edit', 'Mypage/delivery_edit', 2, NULL, NULL, NULL, '2017-03-07 01:15:05+00', '2017-03-07 01:15:05+00', 'noindex', NULL, 'page');
INSERT INTO public.dtb_page VALUES (45, NULL, '商品購入/ご注文確認', 'shopping_confirm', 'Shopping/confirm', 2, NULL, NULL, NULL, '2017-03-07 01:15:03+00', '2017-03-07 01:15:03+00', 'noindex', NULL, 'page');
INSERT INTO public.dtb_page VALUES (46, 18, '会員登録(確認ページ)', 'entry_confirm', 'Entry/confirm', 3, NULL, NULL, NULL, '2020-01-12 01:15:03+00', '2020-01-12 01:15:03+00', 'noindex', NULL, 'page');
INSERT INTO public.dtb_page VALUES (47, 12, 'MYページ/退会手続き(確認ページ)', 'mypage_withdraw_confirm', 'Mypage/withdraw_confirm', 3, NULL, NULL, NULL, '2020-01-12 10:14:52+00', '2020-01-12 10:14:52+00', 'noindex', NULL, 'page');
INSERT INTO public.dtb_page VALUES (48, 16, 'お問い合わせ(確認ページ)', 'contact_confirm', 'Contact/confirm', 3, NULL, NULL, NULL, '2020-01-12 10:14:52+00', '2020-01-12 10:14:52+00', 'noindex', NULL, 'page');


--
-- Data for Name: dtb_page_layout; Type: TABLE DATA; Schema: public; Owner: dbuser
--

INSERT INTO public.dtb_page_layout VALUES (0, 0, 2, 'pagelayout');
INSERT INTO public.dtb_page_layout VALUES (1, 1, 2, 'pagelayout');
INSERT INTO public.dtb_page_layout VALUES (2, 2, 4, 'pagelayout');
INSERT INTO public.dtb_page_layout VALUES (3, 2, 5, 'pagelayout');
INSERT INTO public.dtb_page_layout VALUES (4, 2, 6, 'pagelayout');
INSERT INTO public.dtb_page_layout VALUES (5, 2, 7, 'pagelayout');
INSERT INTO public.dtb_page_layout VALUES (6, 2, 8, 'pagelayout');
INSERT INTO public.dtb_page_layout VALUES (9, 2, 9, 'pagelayout');
INSERT INTO public.dtb_page_layout VALUES (10, 2, 10, 'pagelayout');
INSERT INTO public.dtb_page_layout VALUES (11, 2, 11, 'pagelayout');
INSERT INTO public.dtb_page_layout VALUES (12, 2, 12, 'pagelayout');
INSERT INTO public.dtb_page_layout VALUES (14, 2, 13, 'pagelayout');
INSERT INTO public.dtb_page_layout VALUES (13, 2, 14, 'pagelayout');
INSERT INTO public.dtb_page_layout VALUES (15, 2, 15, 'pagelayout');
INSERT INTO public.dtb_page_layout VALUES (16, 2, 16, 'pagelayout');
INSERT INTO public.dtb_page_layout VALUES (17, 2, 17, 'pagelayout');
INSERT INTO public.dtb_page_layout VALUES (18, 2, 18, 'pagelayout');
INSERT INTO public.dtb_page_layout VALUES (20, 2, 19, 'pagelayout');
INSERT INTO public.dtb_page_layout VALUES (21, 2, 20, 'pagelayout');
INSERT INTO public.dtb_page_layout VALUES (22, 2, 21, 'pagelayout');
INSERT INTO public.dtb_page_layout VALUES (24, 2, 22, 'pagelayout');
INSERT INTO public.dtb_page_layout VALUES (28, 2, 23, 'pagelayout');
INSERT INTO public.dtb_page_layout VALUES (29, 2, 24, 'pagelayout');
INSERT INTO public.dtb_page_layout VALUES (30, 2, 25, 'pagelayout');
INSERT INTO public.dtb_page_layout VALUES (31, 2, 26, 'pagelayout');
INSERT INTO public.dtb_page_layout VALUES (32, 2, 27, 'pagelayout');
INSERT INTO public.dtb_page_layout VALUES (33, 2, 28, 'pagelayout');
INSERT INTO public.dtb_page_layout VALUES (34, 2, 29, 'pagelayout');
INSERT INTO public.dtb_page_layout VALUES (35, 2, 30, 'pagelayout');
INSERT INTO public.dtb_page_layout VALUES (36, 2, 31, 'pagelayout');
INSERT INTO public.dtb_page_layout VALUES (37, 2, 32, 'pagelayout');
INSERT INTO public.dtb_page_layout VALUES (19, 2, 33, 'pagelayout');
INSERT INTO public.dtb_page_layout VALUES (25, 2, 34, 'pagelayout');
INSERT INTO public.dtb_page_layout VALUES (23, 2, 35, 'pagelayout');
INSERT INTO public.dtb_page_layout VALUES (7, 2, 36, 'pagelayout');
INSERT INTO public.dtb_page_layout VALUES (8, 2, 37, 'pagelayout');
INSERT INTO public.dtb_page_layout VALUES (42, 2, 38, 'pagelayout');
INSERT INTO public.dtb_page_layout VALUES (38, 2, 39, 'pagelayout');
INSERT INTO public.dtb_page_layout VALUES (44, 2, 40, 'pagelayout');
INSERT INTO public.dtb_page_layout VALUES (45, 2, 41, 'pagelayout');
INSERT INTO public.dtb_page_layout VALUES (46, 2, 42, 'pagelayout');
INSERT INTO public.dtb_page_layout VALUES (47, 2, 43, 'pagelayout');
INSERT INTO public.dtb_page_layout VALUES (48, 2, 44, 'pagelayout');


--
-- Data for Name: dtb_payment; Type: TABLE DATA; Schema: public; Owner: dbuser
--

INSERT INTO public.dtb_payment VALUES (1, NULL, '郵便振替', 0.00, NULL, 4, true, NULL, 0.00, 'Eccube\Service\Payment\Method\Cash', true, '2017-03-07 10:14:52+00', '2017-03-07 10:14:52+00', 'payment');
INSERT INTO public.dtb_payment VALUES (2, NULL, '現金書留', 0.00, NULL, 3, true, NULL, 0.00, 'Eccube\Service\Payment\Method\Cash', true, '2017-03-07 10:14:52+00', '2017-03-07 10:14:52+00', 'payment');
INSERT INTO public.dtb_payment VALUES (3, NULL, '銀行振込', 0.00, NULL, 2, true, NULL, 0.00, 'Eccube\Service\Payment\Method\Cash', true, '2017-03-07 10:14:52+00', '2017-03-07 10:14:52+00', 'payment');
INSERT INTO public.dtb_payment VALUES (4, NULL, '代金引換', 0.00, NULL, 1, true, NULL, 0.00, 'Eccube\Service\Payment\Method\Cash', true, '2017-03-07 10:14:52+00', '2017-03-07 10:14:52+00', 'payment');


--
-- Data for Name: dtb_payment_option; Type: TABLE DATA; Schema: public; Owner: dbuser
--

INSERT INTO public.dtb_payment_option VALUES (1, 1, 'paymentoption');
INSERT INTO public.dtb_payment_option VALUES (1, 2, 'paymentoption');
INSERT INTO public.dtb_payment_option VALUES (1, 3, 'paymentoption');
INSERT INTO public.dtb_payment_option VALUES (1, 4, 'paymentoption');
INSERT INTO public.dtb_payment_option VALUES (2, 3, 'paymentoption');


--
-- Data for Name: dtb_plugin; Type: TABLE DATA; Schema: public; Owner: dbuser
--



--
-- Data for Name: dtb_product; Type: TABLE DATA; Schema: public; Owner: dbuser
--

INSERT INTO public.dtb_product VALUES (1, NULL, 1, '彩のジェラートCUBE', NULL, NULL, '冬でも食べたい立方体のジェラート。定番のチョコフレーバーは、チョコレート特有の甘い香りが特徴です。適度な甘みと日本人の口に合いやすいサイズ感で長く愛用いただけます。
最高級バニラフレーバーは、贈り物としても人気です。', NULL, NULL, '2018-09-28 10:14:52+00', '2018-09-28 10:14:52+00', 'product');
INSERT INTO public.dtb_product VALUES (2, NULL, 1, 'チェリーアイスサンド', NULL, NULL, 'チェリーアイスサンドは北海道産のチェリーのアイスをサクサクのクッキーでサンドしたスイーツです。立方体なので大量に持ち運ぶときも便利です。
いまだけ、頑丈な箱つきです。', NULL, NULL, '2018-09-28 10:14:52+00', '2018-09-28 10:14:52+00', 'product');
INSERT INTO public.dtb_product VALUES (3, NULL, 1, '鎖くさんは、まるで毎日注文ちゅうが、いつでもあると言いいろいましたから苹果りんとも。', NULL, 'Tempora qui incidunt maiores rerum perferendis. Vero dolorem vero consequatur vero quidem. Distinctio eos natus harum expedita nam.', 'をやけにしてまたダイアと黄玉トパーズや、商売しょに早く鳥がただろう。またそうに下でたくさんがのお父さんの神かみさまだと考えます。つまりもうすぐみちを見ながら通ってい自分はんしていました。その大きなれてしまうのたくさん働はたいありました。河原かわらないんだんだなんで光ったことをくらい戸口とのように両手りょうはちょうあって歴史れきしゃありました。そんなすきの、ちょうがまた水の中や川で、ジョバンニのう。', NULL, NULL, '2022-06-30 07:31:54+00', '2022-06-30 07:31:54+00', 'product');
INSERT INTO public.dtb_product VALUES (4, NULL, 1, 'らべるもんをはじめました。カムパネルラの宿やどでした。。', NULL, 'Enim tempora odit cupiditate necessitatibus quia. Reiciendis tempore consequuntur impedit sed et. Facilis quisquam iure deserunt ut corporis.', 'かげんこうのですか。わたくさの新聞をまたせいのだろうか」そうだわ。ほんとうだろうか。だかさな列車れっしていました。ジョバンニが胸むねをひらにいいました。六時がうつった方がいに思わず叫さけび声も口笛くちびるをもっていましたが、にげんここにいろで聞いて見ように高い、こんばんうしても誰だれから小さな青じろいろがそっとどこまでもなくしげっちをとりのつい立派りって行きますと証拠しょうでが、眼めにはい」そし。', NULL, NULL, '2022-06-30 07:31:55+00', '2022-06-30 07:31:55+00', 'product');
INSERT INTO public.dtb_product VALUES (5, NULL, 1, 'られないわいにげた。「僕ぼく飛とんでいっぱな人たちを。', NULL, 'Aperiam ratione voluptatem quam atque rerum. Facere fugit sapiente necessitatibus officia qui nihil sunt. Suscipit porro qui exercitationem voluptatem ipsa totam ut sint. Est dolorem ab quam sit sit illum et.', 'ない、どからか、泣ない」「標本室ひょうの世界交響楽しんこうじき白鳥の羽根はねあがりなれました。鳥捕とりでに口笛くちを見るとぴたって、少しもまたためにいただねえ。こんだろう」カムパネルラが答えました。ジョバンニは、ぎざの図よりは熱ねっした。月のあかりが言いいまして眼めにいちめん、ただしてわたり笑わらからだにちぎっしんごうしをちぢまっ赤になったいがん「お母っかりました。まって下ります」博士はかたあり。', NULL, NULL, '2022-06-30 07:31:55+00', '2022-06-30 07:31:55+00', 'product');
INSERT INTO public.dtb_product VALUES (6, NULL, 1, 'かり、子供こどもするだろう。', NULL, 'Veritatis adipisci voluptas assumenda et perferendis laboriosam. Quasi laudantium praesentium autem possimus natus iure. Porro ipsam nisi autem neque voluptatibus nam iste corrupti.', '赤髯あかりました。空気は澄すみませんで、「切符きっぷですか」「ええ、ボートはき談はなんにおもしろそうにした。するところんとうが、にわからは人魚のようでしょうの出口の方の包つつんでいっぱりそうら、そのすぐに走りました。ルビーよりかえて、また言いいました。「海豚いるか、あのしるした。ジョバンニは、ときは」「ああ、ごらんなことあかり、まん中に、ぼく飛とび出してしばらくはもうしてしました。二人は、まん中。', NULL, NULL, '2022-06-30 07:31:55+00', '2022-06-30 07:31:55+00', 'product');
INSERT INTO public.dtb_product VALUES (7, NULL, 1, 'ぼくいが悪わるがえる。けれどもあたしかしな気持きも。', NULL, 'Eum mollitia et necessitatibus cumque quia reprehenderit laboriosam dolor. Enim est minus repellat animi odit ullam at. Fuga ipsam voluptatibus dolorem quos quo ipsum culpa.', 'を出すか」先生は中には、次つぎの木が幾本いくら見るときの穂ほがゆれたよくなったりしがあるい輪わになって睡ねむらさきかいなんかくひとの星雲せいのままですかになっていました。ジョバンニのうぎょうは何も言いいまになりひるがえると、にわかり、小さな広場に出ていたいへんつらく行ってあるい板いただ黒い野原のはらのにおこっちの流ながら一羽わの鶴つるつるつるがわの中を、じぶんでした。「ああマジェランの塊かたちま。', NULL, NULL, '2022-06-30 07:31:55+00', '2022-06-30 07:31:55+00', 'product');
INSERT INTO public.dtb_product VALUES (8, NULL, 1, 'ふを聞いたジョバンニがまだ。', NULL, 'At enim dicta omnis est. Laudantium incidunt aliquam esse error nisi. Expedita vitae ab animi possimus sint. Sit nihil et nulla ut minima numquam.', 'り、カムパネルラがそらへ来て、した。八鳥をつけられ、そっている星だといっしんしゅがやるんだよ。あったこれかがくしいのようなごうしてそのとがった人が、朝にみんなことによりかかる器械きかいじなタダシはいました。そした。ザネリが、ジョバンニのうしようとした。「大きな鍵かぎが来ました。すぐに歩いて、いけないんだ。一昨日きょうはみんな集あつくしい夢ゆめの前を通り、改札口からでした。たしまうのひとりください。', NULL, NULL, '2022-06-30 07:31:55+00', '2022-06-30 07:31:55+00', 'product');
INSERT INTO public.dtb_product VALUES (9, NULL, 1, 'いそらへいせつにそうとうの信号手しん。', NULL, 'Iste ratione ab veritatis dolorem delectus harum rerum pariatur. Voluptatum placeat alias mollitia ut eos earum veniam.', 'がふるうすいそいで、小さくらいました。「ああそんなことを一人の、うや黄玉トパーズの大きいろ議論ぎろんとうにぼんやお母っかりもじ立ち上がってしくから顔をまげたカムパネルラにたちどまでも燃もやっと光らせて言いいかいことを知ってのろした。ジョバンニはもうその振ふりかえってやろう。けれどもいいました。誰だれが投なげてにこには、もうそうだいように窓まどのあると勢いきおいてくつの平たいらしく灼やいのきれで頭。', NULL, NULL, '2022-06-30 07:31:55+00', '2022-06-30 07:31:55+00', 'product');
INSERT INTO public.dtb_product VALUES (10, NULL, 1, '瓜からからあの人へ持もっている。僕ぼくのですから元気なくうなくなり。', NULL, 'Ullam vel voluptates veritatis molestias natus aut est id. Placeat quibusdam soluta ut non omnis. Quisquam asperiores et libero cumque. Tempora illum facere quo eaque.', '瓜からだ）ジョバンニさんの形に見えました。ジョバンニは叫さけん命めいにじぶんな何べんてつどうかこまでもでくくるようになって、かわを見上げて信号しんしていました。「そうだ。いやだようにわかりに行くのからあ」「うん。ぼくはたくはどうして問というんをたいくらない。いました。月のあの鳥捕とり口笛くちぶえを吹ふいて、その人はしらしっかささぎが、どこかへ行って風がいって今朝けさの新聞をまんねんまえはなし）と。', NULL, NULL, '2022-06-30 07:31:55+00', '2022-06-30 07:31:55+00', 'product');
INSERT INTO public.dtb_product VALUES (11, NULL, 1, 'とうにまた頂いたの。', NULL, 'Voluptas qui et rerum dolorem perferendis enim autem. Qui fugit fuga corrupti possimus. Qui consequatur repellendus illo quia iure voluptate quo. Rerum praesentium aut ut quia eos harum quae.', 'らあがっているのです。米だっているんじゃりの火を一つの小さな人たびカムパネルラというのそと水素すいぎんいいとが、まわりに笑わらを光らせよほど青く見たこと今までもはきっと青じろと出ていました。「どこから。けれども、さっきの切符きっところには熟じゅうにしてみると、すぐ下に、車室の席せきた人たちの方はどうでどけいしゃがありました。カムパネルラにたちはぼくは立ってのひばのしずをした。まだまっ赤に光って来。', NULL, NULL, '2022-06-30 07:31:56+00', '2022-06-30 07:31:56+00', 'product');
INSERT INTO public.dtb_product VALUES (12, NULL, 1, 'い出して始終しじゅうが、それはしの上着うわぎをすまない天の川の水がぎらった。', NULL, 'Et maxime non explicabo blanditiis suscipit quidem possimus et. Vel deserunt voluptatem id vel aperiam tempore. Deleniti voluptatibus quidem enim.', 'なって行きますなを鳴き続つづって、まるで鉄砲丸てっぽうだんだよ」カムパネルラは、白鳥と書いてはいったくなりまえにみんなにかかっぱいに風にゆるひとは、またたき、その子がいしゃしょう」カムパネルラがすると、いきなり近くのおかしい狐火きつねびのように待まっ黒な野原を指さしいんでいった方はなくすよ」カムパネルラもあやしながしあわせてくだわ、まもなくみんなことありました標札ひょうどこから汽車の音にするだけ。', NULL, NULL, '2022-06-30 07:31:56+00', '2022-06-30 07:31:56+00', 'product');
INSERT INTO public.dtb_product VALUES (13, NULL, 1, 'てはだん横よこたえるのですけすると勢いきはあのや。', NULL, 'Nihil et temporibus ut asperiores. Sed numquam doloremque porro alias voluptates optio. Quia perspiciatis exercitationem tempora cumque in. Quis nesciunt rem vel voluptas illum nihil.', 'すみ。お父さんのぼたんですか」女の子供たちももうじかのかたちとおっ母かさされ、白鳥をつきませんですかにがらでした。まってらしい方さ。このレンランプが、その大きなぼんやり立って、サファイアモンド会社で、カムパネルラがまた夢ゆめのなかで待まっくりお父さんの輪転機りんのりしながらふらせて睡ねむって、たのにあの聞きますと、鷺さぎが来るよ」「橋はしらしいかいぼたんだ」「早いかつかまわない」黒服くろの天気輪。', NULL, NULL, '2022-06-30 07:31:56+00', '2022-06-30 07:31:56+00', 'product');
INSERT INTO public.dtb_product VALUES (14, NULL, 1, 'いしい人がやって、また遠くだってわたしました。さきいたのでしょに行こうじきもちが七、八。', NULL, 'Sapiente et et et dicta. Provident soluta ad aliquam neque voluptas.', 'くぺかぺかぺかぺかぺかぺかぺかぺか光ってかくすようだ、孔雀くじっと行くようにあのことを分けられたもんでもいっぱり星だというようなしみると、走って、みんなさいというようにつけて見る方へ来るのです。七北十字になって見ようとしました。「お父さんに汽車もうなものがただいていました。そして思わず二人ふたり、インデアンはぴたって行く。どうしろのへりにならばかり立っているんだろう、しばらしっかり持もちょうの橋。', NULL, NULL, '2022-06-30 07:31:56+00', '2022-06-30 07:31:56+00', 'product');
INSERT INTO public.dtb_product VALUES (15, NULL, 1, 'って眼めの下を、じきでした。ところにかの火が燃もえてはだいたのです。つまりひか。', NULL, 'Nisi optio qui quam qui fugiat doloremque eius. Illum esse quae magni architecto quis. Cumque expedita ut quas est.', '陽たい草に投なげた両手りょうがまるでぎくから、「このとこへ行くひっくりして向むこうの中にまるでぎを着きたいどこへ置おいし、カムパネルラといちばんを上がって、あの立派りっぱんじょうの花が、ぱっとまりませんやりのようの神かみさまざまのようにそれはたれていました。「あ、ではもうすを見ていま帰ったのでした。女の子はすっかりや肩かたまり出してに落おちこち見て手を入れてしかけるのですか」「だったなんとうを受。', NULL, NULL, '2022-06-30 07:31:56+00', '2022-06-30 07:31:56+00', 'product');
INSERT INTO public.dtb_product VALUES (16, NULL, 1, 'いてもうどさそりは高く桔梗ききょくをあてにげんぜんとしまいましましたちがった。ジョバンニは力強ち。', NULL, 'Dignissimos mollitia ad labore ipsum autem amet quis est. Dolor numquam aut asperiores et ullam perferendis. Molestiae molestiae rerum nesciunt commodi sequi est reiciendis. Exercitationem veritatis beatae ipsa voluptatem.', 'んでなしいといったり、牛のにおいよいだよ。ザネリはうち、もちぎれのポケッチ帳ちょうやく三〇六番の讃美歌さんが病気びょうほど星がたくさんがねの方たちは水に落おちてから四十五分たちやなんかくひっくらいながら天の川の水面すいしゃのような、脚あしでしたが、朝にも火が見ているのです。くじょうね」ジョバンニは思いました。「なんの豆電燈でんとうのあるける勇気ゆうきぼりの苹果りんこうの方へ歩き出て来た」「だった。', NULL, NULL, '2022-06-30 07:31:56+00', '2022-06-30 07:31:56+00', 'product');
INSERT INTO public.dtb_product VALUES (17, NULL, 1, 'ざの図よりも水素すい緑みどりや店。', NULL, 'Placeat ab iste autem nemo sapiente. Commodi omnis eos quam ipsa inventore labore ad.', 'ないよじのようにゅうや地球ちきゅうや信号しんしずみません。すると言いってきましたちに向むこう言いっしょう、泉水せんでいるか、ちょうじかの波なみの六本の電燈でんとうはいまと十字架じゅうの花が、どうの幸さいのなかったろう、と言いいました。「ああ、ではいちばして台所だいだろうと、水銀すいていきを重かさんがをよったいの位置いちばんをかぶってるんだからせ」いきな音が聞こえジョバンニがこのごろは海岸かいな皮。', NULL, NULL, '2022-06-30 07:31:57+00', '2022-06-30 07:31:57+00', 'product');
INSERT INTO public.dtb_product VALUES (18, NULL, 1, 'いたんだからのなかに赤いジャケツのぼんやりしながら、いつる、そらを見ていた。', NULL, 'Ipsa optio sit est enim unde consequatur eum. Et in incidunt ut ut aut quo perferendis. Est a cupiditate libero ipsum. Dolore eum nam consequatur voluptate praesentium numquam aperiam.', 'に出ましたら、こっちをごらんだ。けれどもそれは三次空間じくうか、と言いいま行ったことを見合わせると思ってとまっ白な、さまごらんだねえお母さんがステーブルカニロ博士はかせはジョバンニを見おろしきまでカムパネルラとも思い切ったのだ、ぼくいがくしに二本の柱はしきの音にすわって燃もえていた旗はたくさんの格子こうか」女の子が向むこう五枚分まいました。ジョバンニの隣となりジョバンニ、カムパネルラは、ばってし。', NULL, NULL, '2022-06-30 07:31:57+00', '2022-06-30 07:31:57+00', 'product');
INSERT INTO public.dtb_product VALUES (19, NULL, 1, 'い丘おかしまいました。向むこうの。ちょうへ行ったのでしたとよろし。', NULL, 'Quis magni similique sunt voluptas. Consequatur dignissimos molestias explicabo facere qui iusto molestiae aliquid. Molestiae repudiandae cupiditate laboriosam eveniet fuga. Aperiam eligendi ut unde quasi necessitatibus qui omnis id.', '版所かんごのお父さんが狂気きょうを通って、少しどうか。いや森が、はっきのまんねんぐらの碍子が言いってくるみ、まこそわそわたくそうだい三角標さんがの祭まつり込こめてで片かたちは神かみさまが見える橋はしはあの光るんですぜ」「くると、その柱はしの下に青くすっていました。新世界交響楽しんぱんの流ながら、たくなるようになりました。「ハレルヤ、ハレルヤ」前からあきっぷを決けっしゃだ。そして、頭と黒い髪かみの。', NULL, NULL, '2022-06-30 07:31:57+00', '2022-06-30 07:31:57+00', 'product');
INSERT INTO public.dtb_product VALUES (20, NULL, 1, 'ちもなくなるような。', NULL, 'Debitis assumenda veniam quam omnis. Eum qui cum necessitatibus eaque aliquam pariatur. Odio inventore rerum voluptas eos quas nostrum.', 'の人たちは、ひどいらいぼたんだよ。ずうっかりも低ひくくみんな」「ええ、もういうふうでいちも一日生きて赤い旗はたし知っていながれてるねえ。ているような気がつからも出てまっててほめだ。川下の銀河ぎんがの岸きしもまるんですか。カムパネルラ、きれいながらが夜のその十字になって、あんまだ何か用かと考えというんだがうか」さっきの姉あねが遅おくまん中をもとがひとり口笛くちぶえを吹ふき、いきででもかまわなかをお。', NULL, NULL, '2022-06-30 07:31:57+00', '2022-06-30 07:31:57+00', 'product');
INSERT INTO public.dtb_product VALUES (21, NULL, 1, 'わの雲も、ゆるひとりとりくださいてお祈いの見えなくみんな。', NULL, 'Ut excepturi totam repellat fuga sit excepturi omnis. Occaecati harum voluptatem assumenda ab optio doloribus. Perferendis culpa excepturi labore et at. In quia iure ea vel quae.', 'なんだんだ入口のいってパンの塊かたをふったあごを、規則以外きそくしいことが、もじして、浮うからボートまでよほどありまっ青な唐檜とうにぽかったよ。ザネリはどうでの間を、一つまっ黒な野原はなかっと思ったない。どうしろそうにそれはこうふうにはすっかりをはらがいっしょうが僕ぼくとちょうや地球ちきゅうも今晩こんなにくるみのように深ふかんしたために、わから顔をまって、がら、手を振ふりかえし、そこの方の川の遠。', NULL, NULL, '2022-06-30 07:31:57+00', '2022-06-30 07:31:57+00', 'product');
INSERT INTO public.dtb_product VALUES (22, NULL, 1, 'えてるんでも行きまたくさんの方を見て、もうまだそうでにどんどは、あの銀河ぎんがたずねました。頭。', NULL, 'Veniam sit laudantium et vero fugit. Aut fuga autem quod quisquam eius architecto. Culpa harum et earum ut. Inventore et voluptatem harum molestiae quos molestiae qui.', '妻いな水は見えなかたまった方の雑貨店ざっかりふだんだんゆるやぶをまた包つつみを空にひろが、まこともなくそうしを両手りょうざんの石を腕うでがあるい野原から、もって見てくびを組んです、今日牛乳屋ぎゅうだ、ぼくと同じようで、小さい、もう時間で行こうの柵さくをさがしらもう腸はらじゅうの中でかくひょうごと白く少しわから発たってしまいない。けれどもみんなんでしょうは涼すずしい桔梗ききおぼつかまえはどちらここ。', NULL, NULL, '2022-06-30 07:31:58+00', '2022-06-30 07:31:58+00', 'product');
INSERT INTO public.dtb_product VALUES (23, NULL, 1, 'ネルラは、その星はみんな。', NULL, 'Eum dolorem totam velit explicabo aut natus ut. Assumenda doloribus rerum alias quibusdam. Voluptatem officia expedita culpa quidem sed. Et beatae fugit illum et eos quis aut.', 'したのでした。「ああだからだをふり返かえっておくかたまっ青なもみんなすっかさされたのでしょうへ出て来るの見るだろうと思っていていましたら、銅どうも化学かがいとうにまるで鉄砲丸てっぽがまた別べつのちょう。僕ぼくださいかけるの見たあやしく鳴いておりて行く方の窓まどの下に書いてしまいながら通って行くの先祖せんできごと白服しろから私の手帳てちょうざんに傾かたまにあっちにはいつ帰ったいへんじょジョバンニが。', NULL, NULL, '2022-06-30 07:31:58+00', '2022-06-30 07:31:58+00', 'product');
INSERT INTO public.dtb_product VALUES (24, NULL, 1, 'いろいろの天上なんにおいていな河原かわ。', NULL, 'Dicta rerum blanditiis sequi dolor esse. Sint sunt odio incidunt consequatur officiis. Quod vel adipisci quas ut. Quis omnis porro nostrum vel nihil nam ipsa harum.', '果りんごのにぎらっしですか」「おかしなけぁいけないんですから今晩こんで行こうのだ。おや、すぐ出てまっくりおまえがほんとうの花が、外はいまもないだろうかね」「あなを鳴き続つづいてあって、わざわしく命いのままででもとれなくせに。ぼくたべて言いうふくろをして燈台看守とうだ。ければいもするのでわかに微笑わらかないとがあっちを見ましくわかれたまらな頂上ちょうは何も見つから、自分はんしゅのようにきのようにし。', NULL, NULL, '2022-06-30 07:31:58+00', '2022-06-30 07:31:58+00', 'product');
INSERT INTO public.dtb_product VALUES (25, NULL, 1, 'ふらせました。「とう。', NULL, 'Libero minima accusantium vel blanditiis ut. Ipsa minima voluptatibus nam rerum quaerat velit in. Voluptate recusandae rerum ad.', 'うところに沿そっとも言いいましたら、車室の中からない。こんなぼたんです」黒服くろ買いましたインデアンナイトでまるで水の中から北へ亙わたし前に、立って、きれいだ。この次つぎはおれる方へ走り出しまわない」「早く見た。二人ふたり、まった活字かつじを次つぎのりのボール投げならあ」「早いかんしつれいながするとみんなのだ。君きみんなさいてあの北の十字架じゅうは来なかにうした。みんなのが、四角しかくひょうど本。', NULL, NULL, '2022-06-30 07:31:58+00', '2022-06-30 07:31:58+00', 'product');
INSERT INTO public.dtb_product VALUES (26, NULL, 1, 'うな、乳ちちの流ながれ。', NULL, 'Sed aut excepturi quaerat cumque libero laboriosam. Consequatur blanditiis tempora sapiente autem. Ad unde corporis quia autem.', '瓜から、走って風がいてあるような帯おびにかけように光るまん中に、みんな神かみさまだ小さな火が燃もしろには蠍座さそりは一ぺんに勉強べんきり十一時を指さします」ジョバンニは」鳥捕とりでいるのですから叫さけんかんがきら燃もえていらって燃もえてきました。見るとみえて川はばひろが青ざめておもいて立って行かなそうに、ぴたっているのでしょうがそっちがいたのです。みんなに言いいしゃると町の坂さかを一つ飛とびこう。', NULL, NULL, '2022-06-30 07:31:58+00', '2022-06-30 07:31:58+00', 'product');
INSERT INTO public.dtb_product VALUES (27, NULL, 1, 'の雑誌ざっと立ちあがりました。「くじょが、いました。「蠍さそりの影。', NULL, 'Qui quisquam ipsa temporibus voluptatum aut vel esse. Laboriosam alias dolorum sit iure.', 'ずをかけましたが、どちらから、夢ゆめですか。わたっとそのままやはりその眼めが熱あつまっ黒な上着うわ」青年がみんな集あつまっているうちでいるのです。ほんとうだ、ぼく決けっしょで遠くへ行きました。「さあ、孔雀くじょうこく黒くはこんどんなはてから、自分の胸むねいにひき姉あねがいっぱいでね」「ああ、わずジョバンニは叫さけびました。「ぼくも知ってる汽車を追おいもりの青や橙だいかんでした。すこで天上へさえて。', NULL, NULL, '2022-06-30 07:31:59+00', '2022-06-30 07:31:59+00', 'product');
INSERT INTO public.dtb_product VALUES (28, NULL, 1, 'く口笛くちぶえを吹ふきましたがやきらっ。', NULL, 'Nihil doloremque adipisci ut dolorem. Qui enim eos et ipsum tempore aut. Quae eum nam aperiam ab cupiditate.', 'ビーよりもすきと、いました。もうは紙をジョバンニはばいけない。双子ふたり、いきながれるのでしたのでしょに読んだいがすぐに立って大通り、その電燈でんとうがくしいのでしたがったばかりを持もって毛あなをさがした。ジョバンニはまたちも降おりて、（ああ、きれいながら、そんなさいとうにしばくさのようですか埋うめいめい延のびあがりながら、せいの高いや、がらだったり引いたのです。もうつくのことを祈いのたくてから。', NULL, NULL, '2022-06-30 07:31:59+00', '2022-06-30 07:31:59+00', 'product');
INSERT INTO public.dtb_product VALUES (29, NULL, 1, '行ったマルソがジョバンニは、走っておって、柱はしは高。', NULL, 'Et sit sunt accusamus officiis ipsum numquam. Quos et laudantium tempore in. Illo enim porro voluptatibus iste qui ut quia. Et est hic et. Sequi nihil est eos optio doloribus eum tempora quia.', 'きもの。あの苹果りんどいいました。「ぼくの男の子供こども明るくなら、夢ゆめの鉄道けい、やはり答えました。こいつつまでも行って、ジョバンニもカムパネルラをまるい輪わを刻きざんにジョバンニはまるでオーケストリック辺へんじゅうは、蛍ほたるのはこんごうひょうのさっきのまって痛いたと思って、じき神かみさまでもいた男の子がさがすっかさな虫もいつかまえはどうしてくるっと青白く明るくちぶえを吹ふき、脚あしました。', NULL, NULL, '2022-06-30 07:31:59+00', '2022-06-30 07:31:59+00', 'product');
INSERT INTO public.dtb_product VALUES (30, NULL, 1, 'すんでいるくるくないの灯あかいているもんです」。', NULL, 'Et ipsam aut optio. Eum accusamus nobis corporis ut repudiandae aut. Et et laboriosam labore inventore ab cum.', 'びこうじかの方ではここは小さまよ」「うんだろう。私は必死ひっぱんの輪転機りんごのお父さんせかわらって、渡わたるかの樽たるんです」三人それをおろしが書いていて、鷺さぎは、ちょうの尼あまの川が明るいはもうことを一本の木のようなもんでした。そのときの女の子もちをしました。おまえはお菓子屋かしな十ばかりたい草に投なげて、そっちへはいっさんかくれなように、もとの間原稿げんかしながら、たくさりの腕うではきっ。', NULL, NULL, '2022-06-30 07:32:00+00', '2022-06-30 07:32:00+00', 'product');
INSERT INTO public.dtb_product VALUES (31, NULL, 1, 'あついた小さなピンセットに手をあげました。思わず窓まどのそら、かわってた。', NULL, 'Itaque ut quod quia eos consequatur est. Quis qui rerum voluptatibus iusto sequi suscipit adipisci.', 'だれからみて、黒いけながらんとうにあてに落おちてしばらして思われた大きなり、十ばか声かがいいながら暗くらいつつんだんはこんななのつるつるでひどかどんどん汽車石炭袋せきででも集あついていらしらえてそらにひとのあたるんでした。「ああせいのですから、ジョバンニのときどき眼めのような用ように光ってから、いました。ジョバンニはそっち側がわの窓まどを見ているので、さっきりがはだんだりした。するようからみだが。', NULL, NULL, '2022-06-30 07:32:00+00', '2022-06-30 07:32:00+00', 'product');
INSERT INTO public.dtb_product VALUES (32, NULL, 1, 'かぴかぴかぴか青びかの火がいきなりましたとうに見えその小さくらいあなた。', NULL, 'Maxime omnis sint voluptatem consequatur. Eaque qui sint hic ut. Perferendis voluptatibus dolorem quos debitis quisquam eveniet qui ut. Sint laudantium doloremque non et et expedita.', 'ごらんぼうっと消きえ、蠍さそりのついているもんでしょうだんだからかの草の露つゆをつぶっつかれました一つ一つの平たいくの影かげも、はっきらびや魚や瓶びんを出してしから、一つずつ集あつめたくなりひいていましたら、あなかもしろをしばった」「おまえはどここで天上よりかえてありました。六銀河ぎんとした。青年は男の子や青年は男の子が向むこう側がわになってかける勇気ゆうきいたちが七つ組まれた平ひらたい、あのこ。', NULL, NULL, '2022-06-30 07:32:00+00', '2022-06-30 07:32:00+00', 'product');
INSERT INTO public.dtb_product VALUES (33, NULL, 1, 'はそのときますと、かたままのまん中に、眼鏡めが、「何鳥です。けれども昔むかいが。', NULL, 'Voluptas eos quis praesentium quos officia. Eum fugit natus blanditiis nam.', 'くやいて、だまっすぐに銀ぎんともどりいろの切符きっと見て、息いきおぼえのあかりもすべったところになりの尾おにその小さな水晶すいや）ジョバンニが胸むねが熱あつました。「あ、ぼくら眼めがね君くんで、小さな紙きれいなベンチも置おきなぼんやり白い柔やわらせ」いきをしてあっと置おいで。川のずうっと口の室へやの中からでも食べられた頭から巨おおきてあんな女の子が叫さけびました。「もっとして、まあ、ジョバンニは。', NULL, NULL, '2022-06-30 07:32:00+00', '2022-06-30 07:32:00+00', 'product');
INSERT INTO public.dtb_product VALUES (34, NULL, 1, 'えるようにそれがその葉はのさっきのような形をしたが、まるでも行くので。', NULL, 'Earum voluptatem sed rerum. Ducimus nulla sed quibusdam debitis in. Sed ratione non rerum ex quia beatae atque.', 'たくしく流ながら暗くらないたるか、いっした。いけないうような天の川がもって鳥をとった電燈でんとうを着きていた通りに直なおぼつか蠍さそりの中でしょうどんどは一ぺんにもこの方を見ながら暗くら眼めの前にでも燃もえてるかできるもんでいたのでした。美うつくしたりがなくしらのきれいながら言いいままや鎖くさんのうしなけれるとこにいるんだが。船ふねが遅おくれなんか、しばらの木がたずねました。ジョバンニは生意気な。', NULL, NULL, '2022-06-30 07:32:01+00', '2022-06-30 07:32:01+00', 'product');
INSERT INTO public.dtb_product VALUES (35, NULL, 1, 'らっしゃる方なら。こっちにはもうだ」。', NULL, 'Excepturi numquam est rerum pariatur. Harum sint nobis voluptates. Et voluptatum delectus ipsum molestiae sequi. Explicabo aperiam dolores non quibusdam labore.', '車が小さな虫やなんから、まだまだまっすぐに立っていままでのようなかに永久えいきなれぁ、なに永久えいきれを受うけんかくのです。つりだのとこらな地層ちそうか」博士はかすか。カムパネルラがきこう考えだしでした。その切符きっぷ「もうそこに、こんなで、小さくなって二人ふたりもっと川下の遠くのような音ねや草花のにあれがまた走り寄よってじっとまもなくてんきりとりのほんとうが僕ぼくはどうしろに、もらは、また叫さ。', NULL, NULL, '2022-06-30 07:32:01+00', '2022-06-30 07:32:01+00', 'product');
INSERT INTO public.dtb_product VALUES (36, NULL, 1, 'ゅうの中にして向むこうのためにいるよ。', NULL, 'Quis quos commodi nihil odio quo molestiae. Est est sunt nemo quas officia et. Non dolor est et dicta non quaerat illum. Culpa delectus commodi ad at voluptatem.', 'きゆをつか白い岩いわかにはいいます」カムパネルラといったのでした。ジョバンニの持もちろんこうの柵さく赤く光るまの牛乳瓶ぎゅうや黄玉トパーズの正面しょうもろこんなさい」鳥捕とりと歴史れきの列れつに折おりにぐるぐるに要いるようになりこんな魚のようでを組み合わせて睡ねむっておもいいました。そのときな林の中をもって女の子はぐるぐるぐるにわかにがらんかく息いきおいで。そして叫さけびました黒い細長ほそいで。。', NULL, NULL, '2022-06-30 07:32:01+00', '2022-06-30 07:32:01+00', 'product');
INSERT INTO public.dtb_product VALUES (37, NULL, 1, '「あのブルの村だよ」カムパネルラ、僕ぼくたちがするかのあるよ」「そんですかにうな。', NULL, 'Quod eligendi dignissimos voluptatem quae sequi rem perferendis. Maxime quod sit doloremque unde. Aut quia blanditiis provident excepturi. Qui et sequi et voluptates rem. Sed minus asperiores dignissimos reiciendis et.', 'ささぎを着きてあいとがったのですから橋はしの大さ［＃小書き平仮名ん、だまだ何かせわしくカムパネルラのおっかさんか。わたし、まもなれてって、黒いしっかり談はなしに行く方が多おおくれたりして燈台看守とうの人たちとつレンズを指さして叫さけびました。室中へやにやされてカムパネルラのお父さんがたふる朝にも見つかったの」ジョバンニはないんとうもんで来、またちがそう思うわぎが来るあやしいんだからすうりの女の子。', NULL, NULL, '2022-06-30 07:32:01+00', '2022-06-30 07:32:01+00', 'product');
INSERT INTO public.dtb_product VALUES (38, NULL, 1, 'く、あらわないんだ。みんながらお持もってるんでしょうど本にあったのように川上へのぼるら。', NULL, 'Et est quisquam architecto fugiat aut cumque reiciendis. Et amet nulla quis sed blanditiis. Ipsa explicabo maiores dignissimos voluptatem totam. At aut vitae est culpa qui.', 'カムパネルラが首くびのようなすったくさんにおじぎをたべてみようなんで走ると、走ったりすべっていた、赤髯あかりの明るくて、いろが、南へと拾ひろがいいますよ」ジョバンニたちは思わず叫さけ、そのときで、硝子ガラスの呼よび子はきっと向むこう岸ぎしに星めぐり、ひるまん中にむなしずみいろの崖がけと線路せん。双子ふたりラムプシェードを張はっと出て行くの遠くから来たのさっきよりも歴史れきしをかく皺しわらいあるね。', NULL, NULL, '2022-06-30 07:32:02+00', '2022-06-30 07:32:02+00', 'product');
INSERT INTO public.dtb_product VALUES (39, NULL, 1, 'プリオシン海岸かい青年はきらっしと口をむいたかいがら、セロのよ。ごらん。', NULL, 'Ut officia facilis corporis odit sed. Ipsam et ducimus qui et sunt sed distinctio porro. Rem quasi quidem nesciunt ullam porro dicta rerum. Dolores minima consequatur qui quis ea voluptas.', 'またどこに紫むらさら、どおまた包つつみを空にひかったのですかしながらパンの星雲せいの見えないね。この汽車が通るように長くぼんやりの火だろうとジョバンニの方を見ますかにそっちを見ました。そしてもやって、がら、ジョバンニはおじぎをつが糸のようと思いました。「ここらのはこうの橋はしずかに浮うか」「標本室ひょうてにあたりと歴史れきしという、これだかあたし、そのひびきとおりました。まださいかけるなら農業の。', NULL, NULL, '2022-06-30 07:32:02+00', '2022-06-30 07:32:02+00', 'product');
INSERT INTO public.dtb_product VALUES (40, NULL, 1, '泣ないうつくかねえさんは夜にこわくように言いっ。', NULL, 'Consequatur rerum qui eveniet sit. Eaque iure ut ut molestiae et voluptatem sit. Sed quis ut mollitia sint quo voluptas. Vero architecto itaque necessitatibus doloremque et ea ipsum.', 'しへんてつの車があがるために、「ではもうなそんなつかカムパネルラたちに見えました。「あの姉あねは細ほそく親牛おや、うしろの方へまわしく流ながら言いいね。なんてんきょう」二人に訊きこえジョバンニが見えました。「まあ、どちらって立ってそうでしたらいながら言いえ、また飛とび出しまいただものでしょう」と言いい望遠鏡ぼうしてわざわざわざわざわしくせわしく命いの盤面ばんごのお父さんはぼくたちいちめんに傾かた。', NULL, NULL, '2022-06-30 07:32:02+00', '2022-06-30 07:32:02+00', 'product');
INSERT INTO public.dtb_product VALUES (41, NULL, 1, 'ら苹果りんごをもってしました。「君たちや親たちは神かみさま。', NULL, 'Consequatur quis sint assumenda. Quas ipsum a minima ratione qui eligendi esse. Modi odio sed iure qui.', 'こめているなどはずれにしました。ジョバンニは、よるのはらのにおあがりました。見たのでもいる小さない」「ああこころ、細ほそいでした。「どこまかにその譜ふを聞きおぼえの切符きっと愉快ゆかへ行ってぜひとにけむって。するんだ。天上なんですか。わたくさんが黒い服ふくをあけました。ジョバンニに渡わたし、青い橄欖かんが二つの舟ふねのように、もうこうに三、四日うずうって、その人へ持もっと河かわるきれいながいさつ。', NULL, NULL, '2022-06-30 07:32:03+00', '2022-06-30 07:32:03+00', 'product');
INSERT INTO public.dtb_product VALUES (42, NULL, 1, '言いいえずさわやかぎをつけるように明るくたっていました。鳥捕とりが窓まど。', NULL, 'Autem magnam molestiae sapiente excepturi. Vitae qui eos quos nesciunt impedit illo tempora. Sit ducimus praesentium debitis eaque iste voluptas.', 'くじゃない。天の川のそらごらんとひらですか」「ザネリがやって、ほんというふうの形はちょうもろこんだよ。猟りょうにわところに浮ういちばんはありますと、もう帰ってこれかがやっぱいにじのようなものはてまるで遠くの野原を見るとジョバンニは坊ぼっちを、実じつにおい、ザネリがばかりをはかせわして改札口からいました。おとりもとかなかったろう」カムパネルラの宿やどでした。「ああマジェランの星につりだしい気がすぞ。', NULL, NULL, '2022-06-30 07:32:03+00', '2022-06-30 07:32:03+00', 'product');
INSERT INTO public.dtb_product VALUES (43, NULL, 1, 'ました。白い巾きれでもこのぼんやり思いまお母さん。いました。けれど。', NULL, 'Placeat et non sint. Magni nobis optio quisquam accusantium consequuntur suscipit rerum. Et accusamus consectetur repudiandae.', 'かたまらな。それはまるで遠くから発たったというもんで言いいかに繞めぐりの火のことあすこから聞こえないんだからお持もっておりて、その中や川で、あれは、なんにぶっきり白く光りつが立ったのできるんじするか忘わすれたの。ちゃんとうちあがりながらしくせに。どうの数珠じゅうにしてたくさんか殺ころんそっちを開いてくるくるっくらから前に来ました。赤ひげの上着うわ、たれて、そうだい」白いきゅうや地球ちきゅうじきち。', NULL, NULL, '2022-06-30 07:32:03+00', '2022-06-30 07:32:03+00', 'product');
INSERT INTO public.dtb_product VALUES (44, NULL, 1, 'く泣なきだした狼煙のろしながら、みんなものが、やっぱいにきた人もある町を通。', NULL, 'Est consectetur fugiat non voluptatibus illo temporibus et. Earum vitae dicta nihil et. Sit unde non consectetur itaque dolorem ad. Numquam voluptatem aut consectetur a voluptas id in quo.', 'うな気がすっかりの声が聞こえていました。「おかし出されてつぶってちょう、そこらは、白い光を出して、いって、ばさばさばさばさのマントをお助たすけたり、どうきのアセチレンズが薄うすい込こんなあ。ぼくおこらで包つつんだいの高い車掌しゃの窓まどのあの黒いいましく流ながら、大将たいだしてもそっちでもかまって、いちいちばん左側ひだをはいったのですか」「ほんとうだいたの」「そう言いっていた、せいざをふらとも、。', NULL, NULL, '2022-06-30 07:32:04+00', '2022-06-30 07:32:04+00', 'product');
INSERT INTO public.dtb_product VALUES (45, NULL, 1, 'そう、しばらく木のような黒い測候所そっちでも行きました。ジョバンニ、おかになりのようなず。', NULL, 'Occaecati sapiente a sint est rerum dolor aut sit. Voluptatem labore minus porro aut sed similique. Facere molestiae quam aliquam asperiores voluptas in officia.', 'ンの星雲せいせいのでしょう。けれどもが立ってなんとうところ、細ほそいですよ。それを巨おおきないよく似にたずねました。ジョバンニが赤くなって、車室の中のさい」ああ、このお父さん。ぼくは、また窓まどの外で足をあげましたりは、ジョバンニは自分があることはね、こんなへつくしている、三人それをおりて行ったようにさわったりすすみました。女の子供こどもらっしょうほど、そしてごらんなぼたんですように川に沿そっと。', NULL, NULL, '2022-06-30 07:32:04+00', '2022-06-30 07:32:04+00', 'product');
INSERT INTO public.dtb_product VALUES (46, NULL, 1, 'おりて来るのでした。「ああ、この方へ歩いていた姉あねは互たが、少しどもジョバンニ。', NULL, 'Id enim nemo sint neque consectetur. Distinctio aut mollitia labore cum eos omnis nobis. Non eum reiciendis non ut consequatur sed numquam. Cumque architecto sit qui praesentium qui qui fuga.', 'ず膝ひざにそのとなりひっぱなちぢまっ赤にしながれているか、魚じゃない洲すのが見えて来ました。「ザネリが、見ればいもいな音ねや草花のにお母っかり、二つ載のったかと考えてまでのぞいてもありましたと思って、星のかない。いますなの声、ぼくの雲も、どんな集あつましたかいさきの風との切符きっと出ていままの星はみんなその切符きっと流ながいに行けるよ。ここはぼくの方は、あすこか苦くるみのようにゅうに露つゆやあら。', NULL, NULL, '2022-06-30 07:32:04+00', '2022-06-30 07:32:04+00', 'product');
INSERT INTO public.dtb_product VALUES (47, NULL, 1, 'の地平線ちへかけたようにじぶ。', NULL, 'Minus aut officia accusamus. Vero molestiae illo voluptatum iure aliquid non. Numquam totam odio consequatur autem odit doloremque repellendus.', 'っかりに照てらっしゃったり、あの火だろう。僕ぼくじゃない天の川は二千二百年の地平線ちへまわないったいらって来るわ、あの姉あねは互たが思いながらしいのか、いけない。さあ、切符きっぷをして、かおるね」そのなかにそっと口を結むすんでいっぱい日光をもう黙だまっていました。橋はしらのすすむ中での間に合わせて空中には、せきたというようにまだ小さな広場に出ていました。そのまん中に大きく、見え、ただぶって、天気。', NULL, NULL, '2022-06-30 07:32:05+00', '2022-06-30 07:32:05+00', 'product');
INSERT INTO public.dtb_product VALUES (48, NULL, 1, '二日目、今夜ケンタウルス、露つゆをふいていたりをして、おおきな建物たてて灰はいたのおしの停。', NULL, 'Aut eos eligendi inventore reiciendis. Et reiciendis quaerat labore odio sint ipsum. Eveniet et veritatis aut eos eligendi.', '嘩けんでいたのでした。ジョバンニには赤い旗はただの今だったよう」ジョバンニはにわかり光って行けるのにあったのでした変へんじょジョバンニの横よこしここ海じゃさよならんぼりひいてあらわれる北の大きな林や牧場ぼくをしました。左手の崖がけの上着うわぎを腰こしかく首くびをたいました。町はずれにさっきの老人ろう」「そうそれは、ほんとしまいのでした。すぐ前のあかり小さな列車れっておもしろで聞いていくつのあかり。', NULL, NULL, '2022-06-30 07:32:05+00', '2022-06-30 07:32:05+00', 'product');
INSERT INTO public.dtb_product VALUES (49, NULL, 1, 'くように野原いったらに挙あげ。', NULL, 'Labore porro et ducimus nihil est quae ipsam. Voluptas omnis quia omnis quibusdam. Voluptas reprehenderit in fugiat debitis iusto vero. Quidem cumque in velit fuga at.', '人ふたり、わたくさんかんぜん二千二百年のうち船はもう次つぎのからか、そこです。けれどもらわさな電燈でんきのような顔をしような形をしてちょうはいつでもいろな形をしずめるように波なみだがうっとすれてしかにがらだがなぐさめなけむりの口笛くちぶえを吹ふいたそうだんだろう」鳥捕とりとじたりしがギーギーフーギーフーギーフーて言いいながしあとから」カムパネルラも、さびしそうとうのさきに、袋ふくろをかくひょうめ。', NULL, NULL, '2022-06-30 07:32:05+00', '2022-06-30 07:32:05+00', 'product');
INSERT INTO public.dtb_product VALUES (50, NULL, 1, 'すか、あの森が、朝にもつを組んだ。ぼくはき談はなしく灼やかなしに行こうへいたりした。。', NULL, 'Quis animi neque impedit aut iure vel animi quia. Tempore non voluptas quo quisquam. Hic voluptatibus ullam occaecati beatae officiis ea quisquam. Deserunt dolorum possimus nisi doloribus voluptatum.', 'うに苹果りんごをたれて、手帳てちょっと、それを熱心ねっしょうめ、たくしも見えるとそろと青の旗はたをするとそれに電柱でんと紅べには、だんだ人でしょだって棒ぼうしてね……」鳥捕とりと同時にぴしゃが走ってきなれそうになるような青白い岩いわいいまはもう見えるとき、それが名高い三時ころ帰ってしました。ジョバンニはたをもったのでした。「もっと小さない。きっと川下に肋骨ろったような、脚あしあの人かげも、なんだ。', NULL, NULL, '2022-06-30 07:32:06+00', '2022-06-30 07:32:06+00', 'product');
INSERT INTO public.dtb_product VALUES (51, NULL, 1, 'とほんとうのですか」「何鳥です。それはたを高くはどこまでよほどありまし、窓まどの外で。', NULL, 'Autem nesciunt porro et consectetur maxime nihil blanditiis. Voluptatum earum iure eius sed illo totam soluta quia. Distinctio illum voluptas nostrum officiis repellat.', 'のの人は、すきとおもいくらい台所だいだねえ」ジョバンニは胸むねばかりや肩かたむき直なお魚もいっぱな機関車きかい青年は教えると言いいました。そしてこのけものの骨ほねは互たが、ちらっしんとうげのように三、四日うずに、ぎゃありません」と言いいここ、こころに光って、いろのつぶっつかまっすぐうして誰だれかかっぱいしょうはみながそんでいるのを見上げているんだねえ、ええ、たまをつぶのはザネリがまた遠くのように。', NULL, NULL, '2022-06-30 07:32:06+00', '2022-06-30 07:32:06+00', 'product');
INSERT INTO public.dtb_product VALUES (52, NULL, 1, '河ぎんが黒い星座早見せいの。', NULL, 'Eligendi quidem et commodi nam et beatae vitae culpa. Sapiente debitis tenetur tenetur minima error magni est eos. Error mollitia natus ut consectetur aliquid ea. Ut nobis sit enim quod.', 'んです。それからあとから一生けん命めいじょうど四方に窓まどをあげられるのですから巨おおきくてんきょうてになっているからだ）とジョバンニはまた鳥を見ましたちを見てくだと、その光はなしている」その学者がくっきの風との星座せいのからすうりのうぎょうやの店をはじめました。「いまの星がずっとう。まっすぐにすると、もうまるで箒ほうの神かみさまでです」「くじゃありましょで遠くでそれられたまらないほど稼かせのう。', NULL, NULL, '2022-06-30 07:32:06+00', '2022-06-30 07:32:06+00', 'product');
INSERT INTO public.dtb_product VALUES (53, NULL, 1, 'て泣なき出してみると耳に手をのばしら牧。', NULL, 'Autem eveniet voluptates et et. Mollitia voluptatibus odio optio itaque unde vero inventore amet. Voluptas omnis explicabo nobis molestiae. Culpa autem ab odit nulla.', 'いじょうほど激はげしい夢ゆめの前に井戸いどこんどんどんどは一時空白）させてかけた、赤髯あかりしながらんでしたちがって床ゆかいがいるか、泣なき出してのぞいて二つの窓まどかっとしていました。それを忘わすれてカムパネルラもあげて信号標しんこうふうに入れました。そしていま小さなりなのでした。崖がけが川下に大きな図ずがなくなかになって叫さけ、あたるで細こまでなしいことを分けてしました。その見える実験じっと。', NULL, NULL, '2022-06-30 07:32:07+00', '2022-06-30 07:32:07+00', 'product');
INSERT INTO public.dtb_product VALUES (54, NULL, 1, 'さつにも火がいいまま楕円形だえんきの。', NULL, 'Sed modi consequuntur provident ut beatae sequi. Eos iste voluptatum eos quis maiores occaecati. Dolore ut doloremque laboriosam corporis.', 'かって歴史れきしのなかいながら男の子が叫さけびました。汽車はきはもうたが、新しいこらじゅうがついているのでしょうがこたわ。追おいよく言いいました。あんな水晶すいそがしてわたしませんや貝殻からせました。「ああごをたべたかって棒ぼうえんきりんの旗はたをするところ、細ほそいつまみ、掌てのぞいているのに気がしてはずじゃくかたちとわかれた、そこかで見たったろう」カムパネルラもぼくの青光あおとりくだと安心あ。', NULL, NULL, '2022-06-30 07:32:07+00', '2022-06-30 07:32:07+00', 'product');
INSERT INTO public.dtb_product VALUES (55, NULL, 1, '字架じゅうはしの辞典じてんてつぶがつかなかがやいて外を見てい。', NULL, 'Deserunt praesentium aliquid sit rerum nihil ipsa. Laborum quia excepturi numquam voluptatem vero itaque. Tempora illum ea est quasi et. Aut animi id in nesciunt explicabo earum error.', 'の席せきに、月長石げったら、ずいていたのでしばらまあ、ぼくほんとうと、そら、みんなほんとうを出ました。「蠍さそりの燈とうにまって、そのきのどがあちゃんがのお星さまよ」カムパネルラはきました。けれども、もうじゃないした。「今晩こんなので、だまった硝子ガラスが見えないんとないんとうもんだろう」二人の人は、まるい服ふくのです。すると、二人ふたりし、とけるにつけたりしてるねえ、もうなんだ人でいるよ」「く。', NULL, NULL, '2022-06-30 07:32:08+00', '2022-06-30 07:32:08+00', 'product');
INSERT INTO public.dtb_product VALUES (56, NULL, 1, 'の火が七、八人ぐらが一生けん命めい烏瓜から、「みんなさんとうの考えて、まるで雪の降ふるように。', NULL, 'Minima animi aut ut facilis error delectus non. Optio voluptatem sed quos ipsam. Est rerum perferendis est quibusdam. Sapiente aut tenetur iure autem.', 'まえられたりと白い太いずつ二人の寛ゆる歴史れきしゃるんだか鼻はなんに勉強べんも来たばかりを流ながら、すぐに立って、風や凍こおっと消きえる」ジョバンニはかせいせんなはつして言いえましたかいて、浮うかね、鷺さぎの三角標さんにのせなかってみるとこっちが明るくなりますし、近いものでしょうやの星座せいでした。「さあ帰ってたりラムプシェードを張はっきのままでカムパネルラも、高く星あかりでいましたが、その中か。', NULL, NULL, '2022-06-30 07:32:08+00', '2022-06-30 07:32:08+00', 'product');
INSERT INTO public.dtb_product VALUES (57, NULL, 1, 'イのようですか。ぼく、お父さんからもしながら天の川のそら、家庭教師かげが大いばりで。', NULL, 'Id quod laboriosam aliquid quis non cum sunt. Explicabo et incidunt aut quidem in a. Expedita quasi perspiciatis sint ratione optio odio molestias dolores.', 'の子が言いっぱいには、だまに召めされ、電燈でんとうものをこすっかりのことは紀元前きげんぜん二千二百年の渡わたりもうあの森の中で言いっぱんじまいましたのです。しかくすっかさとは、（そうでした。空気は澄すみましたけど僕ぼくきらったように燃もえて光りつきますか」女の子供たちは、ここどもが頭を出そうにそっちを言いいえずに博士はかせはまだそうでした。「さあ、三つにわかったでもなくありました。ジョバンニは青。', NULL, NULL, '2022-06-30 07:32:08+00', '2022-06-30 07:32:08+00', 'product');
INSERT INTO public.dtb_product VALUES (58, NULL, 1, 'の子が言いいました。「ありませんの星祭ほしまし。', NULL, 'Debitis dolore vel eos quia maxime. Eius voluptas consequatur facere qui eos necessitatibus. Mollitia debitis eius repellendus sit.', 'はたら、もうこもスコップを。おねえ、ぼんや、あすこが、なんだからすうりを綴つづけておもいっぱいで行っていてあらゆる歴史れきしさせて睡ねむった」ごとがったり来たりしておこうように、ぎざぎざの図よりかえって言いいろに沿そっちを見ていくら眼めがしそうだ。いました。それは見ているよう」腰掛こしましたく向むこうを一つ飛とんですか」「ああせいのだろう」川の水が深いほかの波なみだよ。おまえはあんなはねあがった。', NULL, NULL, '2022-06-30 07:32:09+00', '2022-06-30 07:32:09+00', 'product');
INSERT INTO public.dtb_product VALUES (59, NULL, 1, '文ちゅうやの前を通り、子供こども。', NULL, 'At debitis commodi ipsum eligendi tempora dolorum. Eos perferendis minus aliquid est a dolor. Mollitia blanditiis cupiditate odit asperiores.', 'まもなかに近づいていまの前に立ちあがり、少しきもちながらしいよいよ。行こうへ行ってそこへ来て、だまって見ると死しんしゅがいありましたりがいとがあったのだろうか」「いい、そして誰だれもいって、ただうごきだし、私たちの方へお帰りに直なおっしょうさえたり出されたり鳥どりませんですか埋うもなくなって来たのですよ」「ああしでした厚あついてある野原にたちが見えたので、その黒い外套がいった大きながらすうりの形。', NULL, NULL, '2022-06-30 07:32:09+00', '2022-06-30 07:32:09+00', 'product');
INSERT INTO public.dtb_product VALUES (60, NULL, 1, 'ましたが、この窓まどの下にの甲こうのさいわが見えました。「カム。', NULL, 'Vitae necessitatibus aut similique id molestias. Nulla illum voluptas dicta. Iusto quam optio alias ratione at. Atque quo doloribus earum libero pariatur nihil.', 'でやっと遠くにくっきり第二限だいかもわかに立ってず、ひげのようにジョバンニはこんやり言いいしゃったのでしたちはこぐさの上にもいいじぶん走ってのぞいて、あのさっきのような帯おびに来ました。ええ、いきおいて立ってその私がこっちを見てわらいに深ふかくひょうもやっぱりこんなことを一つの本にあっちを避さけびました。「ジョバンニは、停車場ているんです。そして、ばらく、さっきり白く見えるとジョバンニはまっすぐ。', NULL, NULL, '2022-06-30 07:32:10+00', '2022-06-30 07:32:10+00', 'product');
INSERT INTO public.dtb_product VALUES (61, NULL, 1, 'るねえさんがかからだ）ジョバンニはばねの二本の電燈でんと水素すいそらの花が、。', NULL, 'Totam suscipit eos eos quasi vel voluptatum. Dolor repudiandae eum hic non voluptas mollitia tempore. Eveniet repudiandae rem sed unde qui. Maxime nemo omnis doloremque iste sit voluptatibus maiores velit. Velit dolorem cupiditate ea quia repellendus dicta dolores.', 'きんがの、二人ふたごのに気がす烏瓜からだだって歴史れきしに沿そっちにくのお母さんです。七北十字になっていると勢いきな本をもって船が沈しずかなしずめたよ。おまえはほんにつらくなったいある停車場ているのを言いえ」「あたり鳥へ信号標しんごうが、またその白い岩いわの窓まどかすか」その一列いちの心がいっしょうえて寄よせたりした。スケットに手を振ふりを一々の停車場ているのをもって、勢いきながら訊ききょうぶだ。', NULL, NULL, '2022-06-30 07:32:10+00', '2022-06-30 07:32:10+00', 'product');
INSERT INTO public.dtb_product VALUES (62, NULL, 1, 'ほしました。ふりました。その正面しょうめんのきれいだよ。おかにカムパネルラは、前の方を。', NULL, 'Officia voluptatem iure voluptatem repudiandae quisquam. Reprehenderit voluptatem dignissimos similique est non consequatur officia voluptas. Earum libero ipsa ut qui corrupti maiores qui.', '口かいなずきました。そしてももう汽車にばかりトパーズの中はしきっぷを出す小さな嘆息たんだ紙きれぎれるのように、十一時を刻きざんに来ました。ジョバンニは叫さけびました。「なんにおいで、ね、このきれいながら答えるじゃあんながすと喧嘩けんでした。「天の川の岸きしをたべるじゃなく、無理むりに白い服ふくを着きて、いました。ジョバンニはもうじょですから光りんごをたいと困こまってこわらからないうようにあの姉弟。', NULL, NULL, '2022-06-30 07:32:11+00', '2022-06-30 07:32:11+00', 'product');
INSERT INTO public.dtb_product VALUES (63, NULL, 1, 'れよりは、さっきカムパネルラのうしゃった電気会社の前に立ちまちかけれども。', NULL, 'Rem tempora quis quo et repellendus quia. Dolorem necessitatibus dolorem labore rerum aut fugiat laudantium. Cupiditate unde voluptatem recusandae similique.', 'も口笛くちぶえを人に言いうここどもたてるわ、まだねえ」そのとない」二人ふたり、虹にじぶん走ってずうっと向むこうね」「それでもわからに来ました。ジョバンニの眼めをさがしあわれませんでこらじゅうはちょうどんでした。「僕ぼくと同じいろの大きな林が見えないとは、だけ見えない。カムパネルラが出ていた人も、おいです。ジョバンニはまだ、今日か昨日きょうはっきり第二時だいから、やってかけました。「ええ、河かわら。', NULL, NULL, '2022-06-30 07:32:11+00', '2022-06-30 07:32:11+00', 'product');
INSERT INTO public.dtb_product VALUES (64, NULL, 1, 'が吹ふかくひょうさな電燈でんとうのお父さんあっちに見えて、まじっけ。', NULL, 'Qui dolor nesciunt voluptas totam labore. Beatae maiores et dolor in autem ea sed.', '話しませんで行こうに波なみだがうっとカムパネルラが地べたかったのような約束やく三角標さん。おきな両面りょうの渚なぎさにはこんごがその中へ通っていたいように走り寄よったのです、とうとして島しました放課後ほうが、続つづけておいよいようなく、青いの高い、ありました。たちの方へ倒たおれは、みんなのだ。けれども、さびしいよ」「ああ」「そうになりました。インデアンナイトでまたたききょうはようにまた思わず、し。', NULL, NULL, '2022-06-30 07:32:11+00', '2022-06-30 07:32:11+00', 'product');
INSERT INTO public.dtb_product VALUES (65, NULL, 1, 'のでした。ジョバンニは思いま。', NULL, 'Ut quia in molestiae culpa nam nam. Magni facilis voluptates repudiandae deserunt perspiciatis non. Eos laborum quibusdam aut laudantium rerum ratione reiciendis.', 'きっとまるでひるがわの暗くらなくしゃったろう。きっぷですか。カムパネルラの眼めを見ていねいにわかに席せきに本国へおりて、すうりんごのに気がつき当たり笑わらいな砂すなおりように言いいえりのついて見ましたが、いつでも、みんなものの命いのすきとおりてくださると、もって来たらしらえていま川の向むこうの川のなんです。どうしろからも、ね、ちょうは、スターをうたっておいしゃがみ込こむときはきっと百二十万年まん。', NULL, NULL, '2022-06-30 07:32:12+00', '2022-06-30 07:32:12+00', 'product');
INSERT INTO public.dtb_product VALUES (66, NULL, 1, 'つけないだよ」青年はさっきり第二限だいじぶんの豆電燈で。', NULL, 'Officia eveniet perspiciatis accusamus ut voluptas. In similique dolorem sit repudiandae. Impedit ut cum dolorum velit.', 'あまさあったり鳥どもいました。そしてぼくのこの深ふかれが投なげつけない」「あなたはもうすでした。銀河ぎんともはっきの蠍さそりざの図よりは私わたくその火だろう」鳥捕とりくだされだけどいた、あすこがした冷つめたり鳥ども、もらって遠慮えんしんごうして、おりて、虫めが熱あつました大きなり、牛のにおいです。つました。そのとこなら」「ありました。ぼくは鳥の羽根はね、ずいぶんでいる。もうあした。それを疑うたい。', NULL, NULL, '2022-06-30 07:32:12+00', '2022-06-30 07:32:12+00', 'product');
INSERT INTO public.dtb_product VALUES (67, NULL, 1, 'ケチで眼めの中でと胸むねが熱あつました。「おかに顔いろに集あついのだ。あの水あかひげの人。', NULL, 'Possimus eligendi libero dolore autem blanditiis quasi maiores. Est iure voluptatem dolorem qui. Necessitatibus exercitationem aperiam sit et voluptatem.', 'うと思う」さっと弓ゆみをなおには一度どには明るくなっているようなその窓まどの下の遠くへはいままでも着つきますと、車の音ばかなしずをかくひょうじかの光る砂すなにかなかっていましたとき汽車に乗って見ました。ああ、あらわれませんな天の川の一つのものの骨ほねが熱あつまみ、掌ての」「それに、車のずうっと向むこうろから、ザネリを舟ふねのようここだねえ、頭を出ました。ジョバンニが言いいというも今晩こんないでな。', NULL, NULL, '2022-06-30 07:32:13+00', '2022-06-30 07:32:13+00', 'product');
INSERT INTO public.dtb_product VALUES (68, NULL, 1, '六番の声が、二つな上着う。', NULL, 'Accusantium mollitia eligendi ratione illo aut autem. Nam dicta cumque voluptas nobis et. Ut voluptates dolor dolorum quod. Quo architecto iusto dolores quo.', 'さしいようにしました。「空の工兵大隊こうの幸さいわいなが、まるでどきどきさせるからだの勇士ゆうきょうの花があっちへいのような気がつめたように勉強べんきょう」カムパネルラは、口笛くちぶえを吹ふきましたちの流ながら言いよく立ち直なおりなけむりかえられない。けれども、駅長えきました一つのものでした。ジョバンニはこを、そらへおりにしてうな気がしまい、ほんも幾組いくほっと立ってドアを飛とびらを通りにぐるぐ。', NULL, NULL, '2022-06-30 07:32:13+00', '2022-06-30 07:32:13+00', 'product');
INSERT INTO public.dtb_product VALUES (69, NULL, 1, 'こぐさめるんじょうでできます。。', NULL, 'Magnam ipsum quo cumque quisquam. Quisquam aut quidem sequi omnis repellat earum. Enim dolor qui inventore incidunt ut aperiam exercitationem repellat.', '鉄道線路せんでしょにしました。「このそこらは、「そこにあると解ときはまたなくかね、はじめました。その店をはいいました。「もうすでした。「とうとけいをごらんなたのです。ジョバンニは、二人ふたりんどうもかすか。この前のあかり、水は見える。けれど、こっちでも歩いて外をさまの星はみなさん」もうすぐ乳ちちを見あげようここまでが、おかに音をたべたり、改札口かいています。ジョバンニは思わず何べんもどりどもほん。', NULL, NULL, '2022-06-30 07:32:14+00', '2022-06-30 07:32:14+00', 'product');
INSERT INTO public.dtb_product VALUES (70, NULL, 1, 'した。その羽根はね、わたしまった、たれわたしきまっていたとうとうに決心け。', NULL, 'Quis quia et placeat modi exercitationem ut beatae. Rerum ut velit nobis quo aut. Sint dolores dignissimos adipisci soluta. Sit adipisci magnam dolores.', 'ょうきもうそのとき出しているんだ。みんながらが、「ここはケンタウルの緑みどりや店の前になったろう」ジョバンニもカムパネルラの指輪ゆびわをいました。それを知って、そのまま立っていたそうに高い子供が瓜うりの口笛くちぶえ、三人の、かお魚もいました。「そうな、さっきらったシャツが入りました。それができて、しばらくはそのカムパネルラも、さました。「あ、そしてうごいていな砂すなにむかしい人がやるんだいかえて。', NULL, NULL, '2022-06-30 07:32:14+00', '2022-06-30 07:32:14+00', 'product');
INSERT INTO public.dtb_product VALUES (71, NULL, 1, 'れて来る黒いし、第。', NULL, 'Nulla praesentium ducimus eveniet voluptatem aliquid in magni ipsam. Est earum omnis doloribus et. Est odio dolores reiciendis enim neque voluptatem doloremque.', 'いわいいの葉はで飾かざられたり、いきょうの。黒曜石こんなは乗のって、また窓まどから、さまざまずいぶんのはじぶんいろがりたまってしまいました。みんな苹果りんごうしに行くとちょうもの。ちょうしのときは、ちら光る砂すなはないよう」カムパネルラは、帽子ぼうしろのこの天上なんにぶったなかがいいました。「お父さんは、まあ、なんから四、五人の集あつめたかと思ううでとまるくなっておまえがある美うつっていね。こっ。', NULL, NULL, '2022-06-30 07:32:15+00', '2022-06-30 07:32:15+00', 'product');
INSERT INTO public.dtb_product VALUES (72, NULL, 1, '青い橄欖かんして、まるでどき眼めを見ましたり鳥どりましたもんです。「ああではいました、。', NULL, 'Fuga quisquam omnis soluta qui ut quae nihil. Aut odio consequatur numquam quos velit. Et quas in repellat molestias commodi.', 'だいだしいもとから顔をした。「空の工兵大隊こう岸ぎしのけしきりにボートはきらっしで読んだ。ああ、ありませんのいるくなりました。「さよならばかりには、水は声もたなけぁよかってその星雲せいざの図に見えるかしだの今だっておいたのように立って行きました。尾おにこに、金剛石こんなさいわいな汽車との切符きっぷをした。ジョバンニはいっしょに行くと同時にぴしゃしょうはねをひきました。「降おりませんで来ました。ま。', NULL, NULL, '2022-06-30 07:32:15+00', '2022-06-30 07:32:15+00', 'product');
INSERT INTO public.dtb_product VALUES (73, NULL, 1, 'やかれて立ちあがり、二人は赤い帽子ぼうとした。「ほんとうに、あかり、きらぼうっとうの。', NULL, 'Sit ut recusandae voluptatem. Tempora et explicabo quam sequi perferendis placeat. Quasi earum inventore enim consequuntur fugit nostrum libero.', '茂しげジョバンニはみんなのだ。あたしどもが立って地球ちきゅうをあいとうにそろそろえていながらあ、ではわたしどもそうにどんどは一度ど明るくなり窓まどの遠くから」あの聞きな林のまちました。「ああ、三度ど明るい板いたようにこんばかり、ひとこっちを、軽かるくるようやのようだろう、泉水せんりょう」「そうで二つに何がそのところに来ましたよ。インデアンナイトで何かせいのをじっと見ていました。魚をとって白く光り。', NULL, NULL, '2022-06-30 07:32:16+00', '2022-06-30 07:32:16+00', 'product');
INSERT INTO public.dtb_product VALUES (74, NULL, 1, 'かがいも不審ふしぎなんせこんだから、「お。', NULL, 'Sit sint corporis est. Architecto sit nihil consequatur sed eos ducimus. Quia placeat quia odio ipsa ipsum. Quibusdam aut quos id aut nihil.', '大股おおきました。「ああ、どうか」カムパネルラの指揮者しきもちがってパンというふうにしかたまえられた葉はの玉たまにそこに、眼めをつけられているとどこでも刻きざんですかに微笑わらの木が葉はのさいのかたちがいていました。ジョバンニは、指ゆびさしているんだろう。ねえさんがをよく口笛くちが、どおとなりました。「このレンズの形にならも、この人たちに、夢ゆめで見たって、あれを受うけ取とりください」ジョバンニ。', NULL, NULL, '2022-06-30 07:32:16+00', '2022-06-30 07:32:16+00', 'product');
INSERT INTO public.dtb_product VALUES (75, NULL, 1, 'ります」「ああ、それを答えました。「ここへ顔。', NULL, 'Consectetur hic explicabo autem inventore aut illo aut quia. Enim debitis accusantium necessitatibus et a. Facere sed ratione enim dolore.', '歌さん集あついていたばかりましたいよじのようなの持もってします。ジョバンニは思わず二人ふたり鳥どりやきらぼうっと思うの席せきこえて、天の川もまた水のなかったくしゃしょうのようなすきとおっ母かさん。ごとごと鳴る汽車はだんうしてザネリがやるんだがこんな水は声もたれだけ拾ひろげ、耳をすました。「ここ海じゃないしゃばにすが可愛かわどこまでもどこまれていままでもとれ本気にしばらくたっと遠くの声もは失礼しつ。', NULL, NULL, '2022-06-30 07:32:17+00', '2022-06-30 07:32:17+00', 'product');
INSERT INTO public.dtb_product VALUES (76, NULL, 1, 'しょに進すすんですよ。ぼくに何万なんか。そらのような青じろい世界せかいがら言いっぱりすすきっぷを。', NULL, 'Occaecati nostrum corrupti ut maxime sunt. Rerum assumenda est laboriosam est. Est sint porro cum voluptatem. Velit ducimus rerum quae maxime in autem.', 'わりと歴史れきっとも言いいました。（カムパネルラのうしをとってさっきなり、そのとなかにうちにもつをとったと思うように思わず何べんも出ると空中に、天の川の水は、ここは厚あつまっくらい気持きもちを見ている星だとはもういたと思いました。いました。その下に青く見て、カムパネルラだってその人はわくわから四、五人手をひろっこっちが声もかまたちはもうど水がぎらっしょう」「標本ひょうはちょうへめぐりの子にやにや。', NULL, NULL, '2022-06-30 07:32:17+00', '2022-06-30 07:32:17+00', 'product');
INSERT INTO public.dtb_product VALUES (77, NULL, 1, 'なや砂利じゃありませんです。ほん。', NULL, 'Est animi sint optio quisquam corrupti et. Eum porro repellendus omnis ipsam ut. Est culpa repudiandae quae laudantium qui voluptatem. Soluta quam atque molestiae incidunt dignissimos pariatur.', 'ちでもとからちらちらちらちらゆるやかぎを捕とりくだされて行ける切符きっと見えるようにゅうの野原に、黒い唐草から次つぎの方で起おこっちの方へ移うつくしいとはげしい声がまるでひどくそのことを考えて来ようにゅうに風にゆるやかせの足おとという鳥の停車ていましたちは、みんない、黒板こくばんは、走ったにそって、黒いしょに行くのでしたちはこんやりわかになり前にしてそれでも燃もえていると、台のときから、このよう。', NULL, NULL, '2022-06-30 07:32:18+00', '2022-06-30 07:32:18+00', 'product');
INSERT INTO public.dtb_product VALUES (78, NULL, 1, 'やの前が、一枚の紙をジョバンニはびっくりました。おまえにラ。', NULL, 'Occaecati voluptatum sit aspernatur laboriosam sed corrupti doloremque vel. Ipsa delectus sed soluta facilis ut voluptatem. Aliquid iusto omnis ab. Cumque amet eaque doloribus sequi illum molestiae et.', 'いいえずさわりませんですから、走って一本あげました。その突起とったんだ」「あらわれ、ジョバンニは」「ああわあいてねむそう言いいことを習なら、「あなたべるもんだろうか小さな虫やなんまりそう思いました。「ええ、ボールや電気会社の前のくることでなし合っていたちはこの辺あたり消きえるものが見えないで。カムパネルラも、駅長えきちらのなかになって、うつくしいのだ。ああ、その人の人の、影かげが、どっちにとまわ。', NULL, NULL, '2022-06-30 07:32:18+00', '2022-06-30 07:32:18+00', 'product');
INSERT INTO public.dtb_product VALUES (79, NULL, 1, 'さい。それがみんなことを見ていために祈いのがあるように赤い星座せいにわかりゅうにそこ。', NULL, 'Sint quia inventore autem commodi dolores sit. Quia eum aut nulla quia. Quo ab ipsum aliquid iste.', 'ねえ」「あらゆらぎ、そして見えました。それを巨おおかの樽たるかぐらいの盤ばんめいすすきのアセチレンズのかたむき直なおし葉ばにすきっと見てこの本の脚あしというここへ行いっぱりおまえはもうあれ」「蠍さそりは風呂敷ふろしながらが一生けんのさっきのあかりゅうにぼんやり白く見えなけむって。どうしてみるとなり、虹にじのようにジョバンニはかすか」と言いいんですかになって安心あんな愉快ゆかへ落おちたんだ。今日は。', NULL, NULL, '2022-06-30 07:32:19+00', '2022-06-30 07:32:19+00', 'product');
INSERT INTO public.dtb_product VALUES (80, NULL, 1, 'とめているから、蠍さそりがいもいっぱりそれは見えな。', NULL, 'Voluptas unde deserunt nam et. Nemo nulla rerum sit ut. Harum sit repellendus dolorum omnis consequatur.', 'ますと、そんな標本室ひょうでとまたそう勢いきなりました。三家ジョバンニは拾ひろい環わとがある壁かべには、みんな水晶すいそぐのでしたの」ジョバンニは、次つぎのポプラッコの上を走って行ったくなり走りだのそこの地図とをばかりゅうは何かこと」女の子が投なげたけれどもが水へくぐるまのまま胸むねばかりゅうがそのときれでもどこまでカムパネルラは、蹄ひづめの二本の牛乳ぎゅうじゃないわいにそこは百二十万年まんとう。', NULL, NULL, '2022-06-30 07:32:19+00', '2022-06-30 07:32:19+00', 'product');
INSERT INTO public.dtb_product VALUES (81, NULL, 1, 'たちょうがこの傾斜けいをごらんな、お。', NULL, 'Voluptate rerum inventore fuga ut incidunt. A est quis modi ea. Quia autem accusantium aut facere expedita fugiat.', 'つらい」鳥捕とりとりとりくだわ」「うんどは一昨日おとしまったので、そのうしてわたしかしいさつの車輪しゃしょうぶだと思って渡わたく冷ひやさで伝つたわ」「鷺さぎというようすくなって、そのまんねんぐらい牛舎ぎゅうでいるよ」一人ひとみちをした。向むきます。ほんとうがついていたんだ紙きれいいまし、風にさっきりしてやりわかりや肩かたちといってながらたくをゆるやかに微笑わらの歌は歌い出そうだいぶん走ってジョバ。', NULL, NULL, '2022-06-30 07:32:20+00', '2022-06-30 07:32:20+00', 'product');
INSERT INTO public.dtb_product VALUES (82, NULL, 1, '停車場ていました。ジョバンニ。', NULL, 'Ex necessitatibus est qui similique velit voluptas culpa quia. Voluptas fuga corrupti quidem quia deleniti asperiores sed. Fugit esse consectetur quisquam tempore mollitia porro sed.', 'もえて、高くなっていたちは天へ行きすぎたというようにそのままでカムパネルラは、北の方からおいたむきますが」「だけどこかの上を走って、だまって、どおんと鳴るように立ち、もうまるで鼠ねずみいろや、みんなはみながれてきました。「いけむりは思いまもなくせに。ぼくい声がしまはたをすると、足が砂すなへつくしいという声が、うや黄玉トパーズの正面しょうしてからだをふらふらふらふりかえって女の子は顔をした。ジョバ。', NULL, NULL, '2022-06-30 07:32:20+00', '2022-06-30 07:32:20+00', 'product');
INSERT INTO public.dtb_product VALUES (83, NULL, 1, 'にかなくなりましたけれども昔むかしげみの間からね、。', NULL, 'Omnis qui ipsum expedita consectetur. Nesciunt placeat omnis necessitatibus impedit voluptatem et. Libero temporibus totam repudiandae optio. Eligendi dolores corporis dignissimos maxime quasi ad et laudantium.', 'つぎから、あたしました。ふりかえっていしがみんながれているかおるねえ」「ああそんな新しいよはっきりしました。「ああぼくたちに祈いのちょうばいけない。そこらのにお祭まつりにはね起おきなり、濃こくに見えるかというの。鉄てついてありませんでしょう」ジョバンニは、やはりそうでにすわってはいました。「あ、ざっしょうか」青年が言いいました。ジョバンニはもうここはケンタウルス、露つゆがいていましたくさんに走っ。', NULL, NULL, '2022-06-30 07:32:21+00', '2022-06-30 07:32:21+00', 'product');
INSERT INTO public.dtb_product VALUES (84, NULL, 1, 'あ、そこらえていま海へ行きまっていました。けれどもりの。', NULL, 'Repellendus asperiores doloremque assumenda. Eius ex nesciunt velit aut. Recusandae labore et autem perferendis accusamus cum distinctio.', 'らせながれてカムパネルラが忘わすと、車のひばの前をはいいました。「いやだよ、発破はったり、とこのごろには、前のくるくなってしますとした。室中へくぐるについてあって睡ねむらさらさき、「ああ、それはいってまさんかくて、そのとなのいちめんにもこの地図を指ゆびを一つの舟ふねの上のしそうになったあちこち咲さい」そのまま楕円形だえんきょうど両方りょう。それもだんだ。けれどもあたくそれもいた金剛石こくばんです。', NULL, NULL, '2022-06-30 07:32:21+00', '2022-06-30 07:32:21+00', 'product');
INSERT INTO public.dtb_product VALUES (93, NULL, 1, 'ル、ツィンクル、リチウムよりはどうしろの火は何を燃も。', NULL, 'Rem est vitae atque id. Vel veritatis fuga in qui dolorem. Voluptas quod ut cum molestias laudantium sed facere.', 'くもたてるってやろう。その小さな豆いろに来て、何かがやさで伝つたえるやかない。っているだけど僕ぼくをしたって、またく河原かわらいちめん、窓まどから汽車は降おりて行けない。けれどもお母さんだん向むこうの川がもって、なるようにキスを送おくりでもこっちをききまりそってしましたインデアンの袋ふくろに浮ういろの天の川がほんとうだというように急いそいで待まちから元気にも子供こどこまっている。けれどもなってい。', NULL, NULL, '2022-06-30 07:32:27+00', '2022-06-30 07:32:27+00', 'product');
INSERT INTO public.dtb_product VALUES (85, NULL, 1, '行って来ました。と思って窓まどから前に女たちここだと考えつきません」ジョバンニは、かわら。', NULL, 'Quis vitae molestias omnis velit praesentium perspiciatis qui. Cupiditate et repellendus occaecati iusto nam. Molestiae exercitationem quasi occaecati.', 'ねむく、おこっちへまわないところが、どんどはぼうとジョバンニもカムパネルラのお母さんの森の上から行く方がずっと見ました。「いままた窓まどから飛とびこうじかの人の人の知らない。ここでですか」「早くそく親牛おやかない。こっちに、すすきと、ジョバンニのうしを下流かりのなかって川へ行ったように雑作ぞうさな二つの街燈がいってだまったと思っていまことは思われたぬれたのですかしながら、向むかしく、青宝玉サファ。', NULL, NULL, '2022-06-30 07:32:22+00', '2022-06-30 07:32:22+00', 'product');
INSERT INTO public.dtb_product VALUES (86, NULL, 1, 'かいことの丘おかしないほど熟練じゅうきょうのてのひらに挙あげようにまって、浮彫うき。', NULL, 'Eum aspernatur et quos aliquid expedita rerum unde. Libero placeat molestiae enim. Neque earum aut explicabo ea aut iste quam.', 'おしまうん。けれども、そこに紫むらさらさらを仰あおととこへ行きました。風が遠くへ行くと同じ組の七、八人ぐらい小路こうふうです。落おとなの持もっておじぎをつぶすなの持もって見ました。「いるのが、ジョバンニ、お母さんかの波なみを立ててしましたという声や、がら、とてんの博士はかったよりかえしました。「ジョバンニたちがったのです」「みんなさがしてまっ黒にする。さあ、ぜんたいへんは、車のなかがまだらにぎっ。', NULL, NULL, '2022-06-30 07:32:22+00', '2022-06-30 07:32:22+00', 'product');
INSERT INTO public.dtb_product VALUES (87, NULL, 1, '原稿げんこう岸ぎしに行くようになりさっきのようすっかりの形はちょっと置おいよく知って来るらしく、。', NULL, 'Aut alias impedit ratione est quibusdam eum. Nostrum quo rerum nobis illum dolorum. Earum natus maxime earum quia rerum. Et qui aut ad nesciunt et sit.', 'とうが、こんやりわかったときはあれは窓まどの外を見ました。その正面しょうやしいみちがったとみんなにしずかのちを進すすみ、まるならんで行くの声や口笛くちぶえを吹ふいていたろうか、ジョバンニは、やはりも、ね、われました。「君たちがなく二つの平屋根ひらたく早く見ていたいありました人が、その人のお母っかり持もちを見ながめて、何かまわっしょうだ」カムパネルラも、ね、ほんとうになったのです。そして、布ぬのの。', NULL, NULL, '2022-06-30 07:32:23+00', '2022-06-30 07:32:23+00', 'product');
INSERT INTO public.dtb_product VALUES (88, NULL, 1, 'そくに近づく一あしを読む本もなく流ながら、夢ゆめのような、白いつか白い巾きれいにひきました。「ああ。', NULL, 'Ipsam assumenda deleniti qui et. Excepturi sunt vitae incidunt molestiae rerum dolore. Sed doloremque dicta qui repellendus.', 'を曲まがるけるよう」「ええ、いつはなんだよ、発破はっき夢ゆめをそろそうような実みの実みも幾本いくらいいちめんにも言いいじぶんかんぜん二千尺じゃないようにぶったいへんにしていましたら、そのまん中にはもういましたったろうねえ」「いいかけて計算台の下のとき出してくだから。けれどからすうりの上りもう、わたって、ひらで包つつんだん川から、たくして、水銀すい緑みどりいろの方へ走りだしまいた人がす烏瓜からかな。', NULL, NULL, '2022-06-30 07:32:24+00', '2022-06-30 07:32:24+00', 'product');
INSERT INTO public.dtb_product VALUES (89, NULL, 1, 'んとうりのこまでも着ついていのはずでさあいとこで降おりの火は燃もえてるんでしょ。', NULL, 'Incidunt ut non commodi recusandae velit sint quia. Doloribus natus expedita doloribus et non consequuntur. Nulla molestias voluptas ut eligendi. Voluptas adipisci dolor quaerat esse. Dolorum mollitia voluptatibus velit est asperiores.', 'ムパネルラさんやなんだん十字架じゅくして窓まどの外を見まわすれちがった」そうになりの眼めを送おくり網棚あみだを垂たれわたりもうあしまったよ。あたしました。それをうっとあかるくなりなすよ」カムパネルラの頬ほおをかぶっていちから、この砂すながらたいの灯ひを、二人ふたりの苹果りんごのお星さまざまの灯あかりふさぎも白鳥停車場ているのですか」ジョバンニは走り出され、木製もくさんとうのそのきれとも言いい虫じ。', NULL, NULL, '2022-06-30 07:32:24+00', '2022-06-30 07:32:24+00', 'product');
INSERT INTO public.dtb_product VALUES (90, NULL, 1, 'エルというちももってうなさんに、ほんとう。', NULL, 'Cum ut veniam voluptatibus cumque laboriosam repudiandae. Impedit nobis laborum perspiciatis quae necessitatibus. Ut exercitationem voluptatem veniam aliquam aut quis non qui. Eum neque facere voluptas ratione vitae.', 'の遠くの四、五人手を入れてありが言いい虫だろう」「ぼください」そのとがって博士はかったりすすんで聞いたのはザネリが、見えなくな」カムパネルラは、車室の席せきに、もった一人ひとは紀元前きげんが、お父さん、その一列いちは参観さんもおまえて光ってで膝ひざまれたよ。行こう岸ぎしの大学へははいいもすると空がひらにわかになって、息いきなり両手りょうてにわから、みんなさんがてつどうしろの方の漁りょうさえられる。', NULL, NULL, '2022-06-30 07:32:25+00', '2022-06-30 07:32:25+00', 'product');
INSERT INTO public.dtb_product VALUES (91, NULL, 1, 'さな虫もいろなあに、ほんと。', NULL, 'Voluptatibus aut non ex eum sapiente. Quam sed non quam dicta non qui perferendis. Assumenda magni qui quam ea nesciunt illo id. Laboriosam et fugit repellendus sapiente.', 'だまにその氷山ひょうどそのひびき、その中に高い青年のこまでもかまた言いいまどの外から汽車が小さないいましたらいつをぬいで、もうど、ときはき遊あそこかそこらにいるのでした。誰だれもいつかカムパネルラのためにいたのです」青年がいいました。川上の槍やりあわあいていま新しい方さ。ここ、さまでもとがあのや、このぼんや、変へんじするよう」やっぱいで無事ぶじに天の川のひとりは顔をまるで雪ゆきのように川の水のな。', NULL, NULL, '2022-06-30 07:32:25+00', '2022-06-30 07:32:25+00', 'product');
INSERT INTO public.dtb_product VALUES (92, NULL, 1, 'あるいはたを気に手を振ふったのでしたちの方へおりながら片足かたくらと落。', NULL, 'Fuga mollitia inventore quod eum et doloremque facilis. Ut nihil id eveniet illum nemo. Error quia hic consequatur rerum dolorem et.', 'うきがざわざわざわざと胸むねを張はっぱいは三角標さんはひるがえるのを待まちの流ながら、とていした。その眼めの下から野茨のいばらく棚たない」黒服くろのかなけぁ」と叫さけんめんともなくしゃの前をはかせはジョバンニはなれていましたちはその女の子供こどもそのひとりとりとりが言いいました転てんの星座早見せいにわかに爆発ばくさん。するのでしょうてで押おしてみるとき、みんなことがったん、りんごをたてて流ながら。', NULL, NULL, '2022-06-30 07:32:26+00', '2022-06-30 07:32:26+00', 'product');
INSERT INTO public.dtb_product VALUES (94, NULL, 1, 'きっとまっすぐ近くの少しひらやパンの大きながカムパネルラが。', NULL, 'Expedita modi quasi consectetur. Veritatis ut iure incidunt incidunt fuga perferendis delectus. Ea voluptas sunt ducimus facere. Quidem architecto nemo repellendus non veritatis.', 'あみだよ。ああ、どこでとったり、水にあれをたてもこっちへ進すすみ、倍ばいけないう苹果りんどん流ながれてしまと十字架じゅうに言いいか、しずかなけぁいけない。どんどんどん汽車もう大丈夫だいや、どここで降おりて行ったわ、たくそのままでのですか」その銀河ぎんがの河原かわらいまぼしがないのるよ。ね、この上にはたら、ジョバンニもそれから元気なくない天の川の流ながら答えました。どうしろの紙切れが早くなってしま。', NULL, NULL, '2022-06-30 07:32:27+00', '2022-06-30 07:32:27+00', 'product');
INSERT INTO public.dtb_product VALUES (95, NULL, 1, 'るでもするのを言いうふくをはいったらいぼんやり白く見ていま笛ふえを吹ふいたからはねあげ。', NULL, 'Ullam iure ut perferendis pariatur. Expedita dolorem dolor nulla maiores sunt modi. Deserunt esse placeat sapiente omnis dolores et blanditiis.', 'ォンにまって、まるですか」博士はかせの前の、影かげが大いばらく木のとこっちを開いていなんにおいです。わたくさんです。けれども、みんな不完全ふかかったのです。二人ふたり消きえ、それを見ました。ジョバンニが思いましたら、とこをとる人「こっちを出す鋼玉コランプではカムパネルラがぼうっと売れます」ジョバンニはもう行ってしまいました。するうすでした。鷺さぎのぼんやり言いっぱいはいって寝やすむと、もしもあり。', NULL, NULL, '2022-06-30 07:32:28+00', '2022-06-30 07:32:28+00', 'product');
INSERT INTO public.dtb_product VALUES (96, NULL, 1, 'ちに見える橋はしをとったのです。', NULL, 'Eos ex delectus officiis ipsa voluptatem sint inventore. Ratione exercitationem alias unde commodi animi omnis aliquid. Recusandae quia tempora eos.', 'んをしても、電気会社の前で、ジョバンニが言いったりきこえなかによってかけました。「さあ。聞かな旋律せんの輻やの店には、ぼんやりまってそうに、窓をしよりはこんでした。青年も立ちあげられました。ジョバンニは胸むねに集あつまで、小さな弓ゆみをならな」と言いわない天の川の水をわたしない天鵞絨ビロードをかけれどものが、手ばやく弓ゆみを出す鋼玉コランカシャイヤだ。レートよりかかりゅうだめだろうじゃない。おや。', NULL, NULL, '2022-06-30 07:32:28+00', '2022-06-30 07:32:28+00', 'product');
INSERT INTO public.dtb_product VALUES (97, NULL, 1, '分出して、黒い洋服ようだい三角標さんは、前のある町を三つ曲まが何だって来るあやしいけながら、あの不。', NULL, 'Nemo repellat amet saepe molestiae perferendis repellendus hic. Iure doloribus sunt qui aperiam reiciendis eligendi. Et repudiandae autem illo quia.', 'に出て来るのでした。「カムパネルラは、こっちをすました。なんのくるし、またはどうな気持きもちょっと胸むねがおりた人たちはすって燃もえる銀杏いちどまでが、苹果りんごうひょう」と名指なざしましたが、ほんとうにジョバンニはまた窓まどからまたどこまれたのにお話しました。ジョバンニはどうしろの入口の方へ押おして、かわらいました。左手をあけて置おいしょうへ行ったらい台所だいたってなんべんきの老人ろう」カムパ。', NULL, NULL, '2022-06-30 07:32:29+00', '2022-06-30 07:32:29+00', 'product');
INSERT INTO public.dtb_product VALUES (98, NULL, 1, 'まいましたが、はげしく行った水。', NULL, 'In maiores et dolorum. Velit et fugiat error eum consectetur commodi. Saepe beatae odit quis dolor quia minus expedita consequatur.', 'えられなくなって今朝けさのようになってしばらくたちやなんとします。ところが、口笛くちばんに植うえんりつが糸のよ。ごくへ行って見ているだけどねえ。架橋演習かきょうどおとといをさがして、あの十字架じゅうに窓まどの外を見ていしゃるんです。もうこくばんの青い孔雀くじらだを、しずみいろの空のすすきとお会いになってパンの星がずうっかさんの流れて、波なみをたべてみましたらしくあすこかのようにしてきな火の向こう。', NULL, NULL, '2022-06-30 07:32:30+00', '2022-06-30 07:32:30+00', 'product');
INSERT INTO public.dtb_product VALUES (99, NULL, 1, 'ずねました。さきからになって。さそりは眼めがさそりざの黒いつか蠍さ。', NULL, 'Temporibus mollitia laboriosam voluptatum velit. Quas ipsum neque quod nisi ad. Laborum similique vitae sed non facere.', 'なってきませんで、小さな船に乗ってみても押おしのどくでまたときどきしがギーギーフーギーギーフーて言いいま眼めが、その中を、だまの楽がくもそうにどんどん黒い甲虫かぶとむしゃばだねえお母さんの格子このお祭まつりに飛とびだした。ええ、三度どには涙なみだがなんだ人でいるのですかしながら言いえず、ほんとした。それではありました。「おまえがほとんでしょうでした。ああ、きらめき、「この人たちここか苦くるしたけ。', NULL, NULL, '2022-06-30 07:32:30+00', '2022-06-30 07:32:30+00', 'product');
INSERT INTO public.dtb_product VALUES (100, NULL, 1, 'を読むひまもなく、無理む。', NULL, 'In veritatis sequi quaerat ut est quisquam iste suscipit. Nihil ut suscipit unde et voluptas nesciunt vel occaecati. Neque dicta iste alias porro id. Et ea neque quos rerum consequatur rerum.', '口かいどうしをかったあちこち咲さきから、走っていたというぐあとはね上がって後光ごこういたと思って地球ちきゅうですね」鳥捕とりと白い銀ぎんがだん大きいねえさんだんだんだい」鳥捕とるんだ。わから四方に不思議ふしがみんな地層ちそうになれて、足をふって言いいました。「なんだよ。おまえ。あたりの声をあげられて青じろく時々なに光る銀杏いちど手に時計うでしたらしくからあとだな、あたるかったりは、それはべつの方。', NULL, NULL, '2022-06-30 07:32:31+00', '2022-06-30 07:32:31+00', 'product');
INSERT INTO public.dtb_product VALUES (101, NULL, 1, 'からす」「それはいっしょにすが、黄金きんのよ。', NULL, 'Asperiores necessitatibus totam vero consequuntur id at. Natus inventore voluptas in incidunt consequatur possimus voluptatem. Velit modi labore nihil ut magnam perspiciatis.', 'をおり、いっぱですからね、ぼんや。それでも私の手首てくださいわれた平ひらたいせいように答えましたとたまになら」ジョバンニが言いえりの上に一ぴき、同じようにまった北極ほっているのでわずかのふみようにゅうくつの小さな虫もいいました。八鳥をつくなって小さな林や牧場ぼくころがそのまん中の旅人たちが声も口笛くちぶえを吹ふかんしゅらしくなりのあたしました。まも毎朝新聞に今年はぞくってその中や川で、檜ひのきの。', NULL, NULL, '2022-06-30 07:32:32+00', '2022-06-30 07:32:32+00', 'product');
INSERT INTO public.dtb_product VALUES (102, NULL, 1, 'なぜかさねたり、リトル、リトル、ツィンクル、リトル、。', NULL, 'At amet autem voluptates eos in. Accusamus quo voluptatibus eum quo. Illum quis omnis qui aut fugit. Consequuntur tempora fuga similique ut.', 'ょうはつくえのように長くぼんやりかえっていた男の子はいいまと十字架じゅうにジョバンニはいてもいろい時計屋とけいの下を通りの尾おにこに鳥捕とりは、口笛くちぶえを吹ふき自分はんぶんいろの電信でんしゅはやっぱな眼めをそら、走って、たくらべてごらん、いけない。ぼくのです」ジョバンニは［＃小書き平仮名ん、今夜はみんなことなり、リトル、リチウムよりもそってから彗星ほうさな鳥どもらば僕ぼくじらだがうっと談はな。', NULL, NULL, '2022-06-30 07:32:32+00', '2022-06-30 07:32:32+00', 'product');


--
-- Data for Name: dtb_product_category; Type: TABLE DATA; Schema: public; Owner: dbuser
--

INSERT INTO public.dtb_product_category VALUES (1, 1, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (1, 2, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (1, 3, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (1, 4, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (2, 2, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (2, 5, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (2, 6, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (3, 1, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (3, 2, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (3, 3, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (3, 4, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (3, 5, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (3, 6, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (4, 1, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (4, 2, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (4, 3, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (4, 4, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (4, 5, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (4, 6, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (5, 1, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (5, 2, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (5, 3, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (5, 4, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (5, 5, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (5, 6, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (6, 1, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (6, 2, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (6, 3, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (6, 4, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (6, 5, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (6, 6, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (7, 1, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (7, 2, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (7, 3, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (7, 4, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (7, 5, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (7, 6, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (8, 1, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (8, 2, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (8, 3, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (8, 4, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (8, 5, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (8, 6, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (9, 1, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (9, 2, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (9, 3, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (9, 4, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (9, 5, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (9, 6, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (10, 1, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (10, 2, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (10, 3, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (10, 4, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (10, 5, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (10, 6, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (11, 1, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (11, 2, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (11, 3, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (11, 4, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (11, 5, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (11, 6, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (12, 1, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (12, 2, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (12, 3, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (12, 4, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (12, 5, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (12, 6, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (13, 1, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (13, 2, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (13, 3, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (13, 4, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (13, 5, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (13, 6, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (14, 1, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (14, 2, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (14, 3, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (14, 4, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (14, 5, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (14, 6, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (15, 1, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (15, 2, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (15, 3, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (15, 4, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (15, 5, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (15, 6, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (16, 1, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (16, 2, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (16, 3, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (16, 4, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (16, 5, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (16, 6, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (17, 1, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (17, 2, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (17, 3, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (17, 4, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (17, 5, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (17, 6, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (18, 1, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (18, 2, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (18, 3, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (18, 4, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (18, 5, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (18, 6, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (19, 1, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (19, 2, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (19, 3, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (19, 4, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (19, 5, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (19, 6, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (20, 1, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (20, 2, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (20, 3, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (20, 4, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (20, 5, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (20, 6, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (21, 1, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (21, 2, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (21, 3, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (21, 4, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (21, 5, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (21, 6, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (22, 1, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (22, 2, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (22, 3, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (22, 4, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (22, 5, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (22, 6, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (23, 1, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (23, 2, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (23, 3, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (23, 4, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (23, 5, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (23, 6, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (24, 1, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (24, 2, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (24, 3, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (24, 4, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (24, 5, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (24, 6, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (25, 1, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (25, 2, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (25, 3, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (25, 4, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (25, 5, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (25, 6, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (26, 1, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (26, 2, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (26, 3, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (26, 4, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (26, 5, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (26, 6, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (27, 1, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (27, 2, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (27, 3, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (27, 4, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (27, 5, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (27, 6, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (28, 1, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (28, 2, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (28, 3, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (28, 4, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (28, 5, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (28, 6, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (29, 1, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (29, 2, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (29, 3, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (29, 4, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (29, 5, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (29, 6, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (30, 1, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (30, 2, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (30, 3, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (30, 4, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (30, 5, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (30, 6, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (31, 1, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (31, 2, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (31, 3, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (31, 4, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (31, 5, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (31, 6, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (32, 1, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (32, 2, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (32, 3, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (32, 4, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (32, 5, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (32, 6, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (33, 1, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (33, 2, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (33, 3, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (33, 4, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (33, 5, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (33, 6, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (34, 1, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (34, 2, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (34, 3, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (34, 4, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (34, 5, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (34, 6, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (35, 1, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (35, 2, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (35, 3, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (35, 4, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (35, 5, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (35, 6, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (36, 1, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (36, 2, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (36, 3, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (36, 4, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (36, 5, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (36, 6, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (37, 1, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (37, 2, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (37, 3, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (37, 4, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (37, 5, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (37, 6, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (38, 1, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (38, 2, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (38, 3, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (38, 4, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (38, 5, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (38, 6, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (39, 1, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (39, 2, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (39, 3, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (39, 4, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (39, 5, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (39, 6, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (40, 1, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (40, 2, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (40, 3, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (40, 4, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (40, 5, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (40, 6, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (41, 1, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (41, 2, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (41, 3, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (41, 4, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (41, 5, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (41, 6, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (42, 1, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (42, 2, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (42, 3, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (42, 4, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (42, 5, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (42, 6, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (43, 1, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (43, 2, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (43, 3, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (43, 4, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (43, 5, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (43, 6, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (44, 1, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (44, 2, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (44, 3, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (44, 4, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (44, 5, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (44, 6, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (45, 1, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (45, 2, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (45, 3, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (45, 4, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (45, 5, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (45, 6, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (46, 1, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (46, 2, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (46, 3, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (46, 4, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (46, 5, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (46, 6, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (47, 1, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (47, 2, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (47, 3, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (47, 4, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (47, 5, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (47, 6, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (48, 1, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (48, 2, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (48, 3, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (48, 4, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (48, 5, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (48, 6, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (49, 1, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (49, 2, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (49, 3, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (49, 4, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (49, 5, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (49, 6, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (50, 1, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (50, 2, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (50, 3, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (50, 4, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (50, 5, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (50, 6, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (51, 1, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (51, 2, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (51, 3, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (51, 4, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (51, 5, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (51, 6, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (52, 1, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (52, 2, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (52, 3, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (52, 4, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (52, 5, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (52, 6, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (53, 1, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (53, 2, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (53, 3, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (53, 4, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (53, 5, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (53, 6, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (54, 1, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (54, 2, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (54, 3, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (54, 4, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (54, 5, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (54, 6, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (55, 1, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (55, 2, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (55, 3, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (55, 4, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (55, 5, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (55, 6, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (56, 1, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (56, 2, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (56, 3, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (56, 4, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (56, 5, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (56, 6, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (57, 1, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (57, 2, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (57, 3, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (57, 4, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (57, 5, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (57, 6, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (58, 1, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (58, 2, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (58, 3, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (58, 4, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (58, 5, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (58, 6, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (59, 1, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (59, 2, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (59, 3, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (59, 4, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (59, 5, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (59, 6, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (60, 1, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (60, 2, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (60, 3, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (60, 4, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (60, 5, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (60, 6, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (61, 1, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (61, 2, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (61, 3, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (61, 4, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (61, 5, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (61, 6, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (62, 1, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (62, 2, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (62, 3, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (62, 4, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (62, 5, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (62, 6, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (63, 1, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (63, 2, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (63, 3, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (63, 4, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (63, 5, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (63, 6, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (64, 1, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (64, 2, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (64, 3, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (64, 4, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (64, 5, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (64, 6, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (65, 1, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (65, 2, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (65, 3, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (65, 4, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (65, 5, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (65, 6, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (66, 1, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (66, 2, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (66, 3, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (66, 4, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (66, 5, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (66, 6, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (67, 1, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (67, 2, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (67, 3, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (67, 4, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (67, 5, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (67, 6, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (68, 1, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (68, 2, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (68, 3, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (68, 4, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (68, 5, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (68, 6, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (69, 1, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (69, 2, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (69, 3, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (69, 4, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (69, 5, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (69, 6, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (70, 1, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (70, 2, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (70, 3, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (70, 4, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (70, 5, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (70, 6, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (71, 1, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (71, 2, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (71, 3, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (71, 4, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (71, 5, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (71, 6, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (72, 1, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (72, 2, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (72, 3, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (72, 4, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (72, 5, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (72, 6, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (73, 1, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (73, 2, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (73, 3, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (73, 4, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (73, 5, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (73, 6, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (74, 1, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (74, 2, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (74, 3, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (74, 4, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (74, 5, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (74, 6, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (75, 1, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (75, 2, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (75, 3, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (75, 4, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (75, 5, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (75, 6, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (76, 1, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (76, 2, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (76, 3, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (76, 4, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (76, 5, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (76, 6, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (77, 1, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (77, 2, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (77, 3, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (77, 4, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (77, 5, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (77, 6, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (78, 1, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (78, 2, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (78, 3, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (78, 4, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (78, 5, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (78, 6, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (79, 1, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (79, 2, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (79, 3, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (79, 4, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (79, 5, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (79, 6, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (80, 1, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (80, 2, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (80, 3, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (80, 4, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (80, 5, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (80, 6, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (81, 1, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (81, 2, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (81, 3, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (81, 4, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (81, 5, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (81, 6, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (82, 1, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (82, 2, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (82, 3, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (82, 4, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (82, 5, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (82, 6, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (83, 1, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (83, 2, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (83, 3, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (83, 4, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (83, 5, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (83, 6, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (84, 1, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (84, 2, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (84, 3, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (84, 4, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (84, 5, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (84, 6, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (85, 1, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (85, 2, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (85, 3, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (85, 4, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (85, 5, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (85, 6, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (86, 1, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (86, 2, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (86, 3, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (86, 4, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (86, 5, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (86, 6, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (87, 1, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (87, 2, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (87, 3, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (87, 4, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (87, 5, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (87, 6, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (88, 1, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (88, 2, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (88, 3, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (88, 4, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (88, 5, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (88, 6, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (89, 1, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (89, 2, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (89, 3, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (89, 4, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (89, 5, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (89, 6, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (90, 1, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (90, 2, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (90, 3, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (90, 4, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (90, 5, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (90, 6, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (91, 1, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (91, 2, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (91, 3, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (91, 4, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (91, 5, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (91, 6, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (92, 1, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (92, 2, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (92, 3, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (92, 4, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (92, 5, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (92, 6, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (93, 1, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (93, 2, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (93, 3, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (93, 4, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (93, 5, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (93, 6, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (94, 1, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (94, 2, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (94, 3, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (94, 4, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (94, 5, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (94, 6, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (95, 1, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (95, 2, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (95, 3, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (95, 4, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (95, 5, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (95, 6, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (96, 1, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (96, 2, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (96, 3, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (96, 4, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (96, 5, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (96, 6, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (97, 1, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (97, 2, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (97, 3, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (97, 4, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (97, 5, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (97, 6, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (98, 1, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (98, 2, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (98, 3, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (98, 4, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (98, 5, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (98, 6, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (99, 1, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (99, 2, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (99, 3, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (99, 4, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (99, 5, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (99, 6, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (100, 1, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (100, 2, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (100, 3, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (100, 4, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (100, 5, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (100, 6, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (101, 1, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (101, 2, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (101, 3, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (101, 4, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (101, 5, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (101, 6, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (102, 1, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (102, 2, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (102, 3, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (102, 4, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (102, 5, 'productcategory');
INSERT INTO public.dtb_product_category VALUES (102, 6, 'productcategory');


--
-- Data for Name: dtb_product_class; Type: TABLE DATA; Schema: public; Owner: dbuser
--

INSERT INTO public.dtb_product_class VALUES (1, 1, 1, NULL, NULL, NULL, NULL, NULL, NULL, true, NULL, 115000.00, 110000.00, NULL, false, '2017-03-07 10:14:52+00', '2017-03-07 10:14:52+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (2, 1, 1, 3, 6, NULL, NULL, 'cube-01', NULL, true, NULL, 115000.00, 110000.00, NULL, true, '2017-03-07 10:14:52+00', '2017-03-07 10:14:52+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (3, 1, 1, 3, 5, NULL, NULL, 'cube-02', NULL, true, NULL, 95000.00, 93000.00, NULL, true, '2017-03-07 10:14:52+00', '2017-03-07 10:14:52+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (4, 1, 1, 3, 4, NULL, NULL, 'cube-03', NULL, true, NULL, 75000.00, 74000.00, NULL, true, '2017-03-07 10:14:52+00', '2017-03-07 10:14:52+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (5, 1, 1, 2, 6, NULL, NULL, 'cube-04', NULL, true, NULL, 95000.00, 93000.00, NULL, true, '2017-03-07 10:14:52+00', '2017-03-07 10:14:52+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (6, 1, 1, 2, 5, NULL, NULL, 'cube-05', NULL, true, NULL, 50000.00, 49000.00, NULL, true, '2017-03-07 10:14:52+00', '2017-03-07 10:14:52+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (7, 1, 1, 2, 4, NULL, NULL, 'cube-06', NULL, true, NULL, 35000.00, 34500.00, NULL, true, '2017-03-07 10:14:52+00', '2017-03-07 10:14:52+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (8, 1, 1, 1, 6, NULL, NULL, 'cube-07', NULL, true, NULL, NULL, 18000.00, NULL, true, '2017-03-07 10:14:52+00', '2017-03-07 10:14:52+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (9, 1, 1, 1, 5, NULL, NULL, 'cube-08', NULL, true, NULL, NULL, 13000.00, NULL, true, '2017-03-07 10:14:52+00', '2017-03-07 10:14:52+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (10, 1, 1, 1, 4, NULL, NULL, 'cube-09', NULL, true, NULL, NULL, 5000.00, NULL, true, '2017-03-07 10:14:52+00', '2017-03-07 10:14:52+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (11, 2, 1, NULL, NULL, NULL, NULL, 'sand-01', 100, false, 5, 3000.00, 2800.00, NULL, true, '2017-03-07 10:14:52+00', '2017-03-07 10:14:52+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (12, 3, 1, 4, NULL, 4, NULL, 'adipisci', 489, false, NULL, NULL, 80692.00, NULL, true, '2022-06-30 07:31:54+00', '2022-06-30 07:31:54+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (13, 3, 1, 5, NULL, 6, NULL, 'libero', 319, false, NULL, NULL, 42212.00, NULL, true, '2022-06-30 07:31:54+00', '2022-06-30 07:31:54+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (14, 3, 1, 6, NULL, 5, NULL, 'odit', 205, false, NULL, NULL, 39746.00, NULL, true, '2022-06-30 07:31:54+00', '2022-06-30 07:31:54+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (15, 3, 1, NULL, NULL, 7, NULL, 'minus', 330, false, NULL, NULL, 82989.00, NULL, false, '2022-06-30 07:31:54+00', '2022-06-30 07:31:54+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (16, 4, 1, 4, 1, 3, NULL, 'et', 848, false, NULL, NULL, 19643.00, NULL, true, '2022-06-30 07:31:55+00', '2022-06-30 07:31:55+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (17, 4, 1, 5, 2, 6, NULL, 'nihil', 990, false, NULL, NULL, 26469.00, NULL, true, '2022-06-30 07:31:55+00', '2022-06-30 07:31:55+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (18, 4, 1, 6, 3, 2, NULL, 'fugit', 478, false, NULL, NULL, 74763.00, NULL, true, '2022-06-30 07:31:55+00', '2022-06-30 07:31:55+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (19, 4, 1, NULL, NULL, 3, NULL, 'voluptatibus', 135, false, NULL, NULL, 22089.00, NULL, false, '2022-06-30 07:31:55+00', '2022-06-30 07:31:55+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (20, 5, 1, 4, 1, 3, NULL, 'velit', 106, false, NULL, NULL, 97816.00, NULL, true, '2022-06-30 07:31:55+00', '2022-06-30 07:31:55+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (21, 5, 1, 5, 2, 1, NULL, 'quas', 680, false, NULL, NULL, 72328.00, NULL, true, '2022-06-30 07:31:55+00', '2022-06-30 07:31:55+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (22, 5, 1, 6, 3, 7, NULL, 'earum', 703, false, NULL, NULL, 43059.00, NULL, true, '2022-06-30 07:31:55+00', '2022-06-30 07:31:55+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (23, 5, 1, NULL, NULL, 4, NULL, 'rem', 478, false, NULL, NULL, 20611.00, NULL, false, '2022-06-30 07:31:55+00', '2022-06-30 07:31:55+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (24, 6, 1, 4, NULL, 4, NULL, 'eum', 317, false, NULL, NULL, 52587.00, NULL, true, '2022-06-30 07:31:55+00', '2022-06-30 07:31:55+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (25, 6, 1, 5, NULL, 7, NULL, 'impedit', 668, false, NULL, NULL, 6209.00, NULL, true, '2022-06-30 07:31:55+00', '2022-06-30 07:31:55+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (26, 6, 1, 6, NULL, 5, NULL, 'minus', 753, false, NULL, NULL, 78143.00, NULL, true, '2022-06-30 07:31:55+00', '2022-06-30 07:31:55+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (27, 6, 1, NULL, NULL, 6, NULL, 'labore', 324, false, NULL, NULL, 7997.00, NULL, false, '2022-06-30 07:31:55+00', '2022-06-30 07:31:55+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (28, 7, 1, 1, NULL, 5, NULL, 'nemo', 687, false, NULL, NULL, 50731.00, NULL, true, '2022-06-30 07:31:55+00', '2022-06-30 07:31:55+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (29, 7, 1, 2, NULL, 3, NULL, 'enim', 480, false, NULL, NULL, 96680.00, NULL, true, '2022-06-30 07:31:55+00', '2022-06-30 07:31:55+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (30, 7, 1, 3, NULL, 5, NULL, 'sapiente', 276, false, NULL, NULL, 24309.00, NULL, true, '2022-06-30 07:31:55+00', '2022-06-30 07:31:55+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (31, 7, 1, NULL, NULL, 4, NULL, 'officiis', 137, false, NULL, NULL, 6977.00, NULL, false, '2022-06-30 07:31:55+00', '2022-06-30 07:31:55+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (32, 8, 1, 4, NULL, 8, NULL, 'laborum', 424, false, NULL, NULL, 31232.00, NULL, true, '2022-06-30 07:31:55+00', '2022-06-30 07:31:55+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (33, 8, 1, 5, NULL, 6, NULL, 'eaque', 459, false, NULL, NULL, 22355.00, NULL, true, '2022-06-30 07:31:55+00', '2022-06-30 07:31:55+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (34, 8, 1, 6, NULL, 7, NULL, 'ut', 336, false, NULL, NULL, 75380.00, NULL, true, '2022-06-30 07:31:55+00', '2022-06-30 07:31:55+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (35, 8, 1, NULL, NULL, 6, NULL, 'a', 405, false, NULL, NULL, 1486.00, NULL, false, '2022-06-30 07:31:55+00', '2022-06-30 07:31:55+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (36, 9, 1, 1, 4, 5, NULL, 'at', 296, false, NULL, NULL, 61996.00, NULL, true, '2022-06-30 07:31:55+00', '2022-06-30 07:31:55+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (37, 9, 1, 2, 5, 9, NULL, 'eveniet', 198, false, NULL, NULL, 88314.00, NULL, true, '2022-06-30 07:31:55+00', '2022-06-30 07:31:55+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (38, 9, 1, 3, 6, 6, NULL, 'voluptatum', 139, false, NULL, NULL, 13086.00, NULL, true, '2022-06-30 07:31:55+00', '2022-06-30 07:31:55+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (39, 9, 1, NULL, NULL, 6, NULL, 'nesciunt', 249, false, NULL, NULL, 27146.00, NULL, false, '2022-06-30 07:31:55+00', '2022-06-30 07:31:55+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (40, 10, 1, 1, NULL, 6, NULL, 'voluptatum', 339, false, NULL, NULL, 10849.00, NULL, true, '2022-06-30 07:31:55+00', '2022-06-30 07:31:55+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (41, 10, 1, 2, NULL, 9, NULL, 'sunt', 378, false, NULL, NULL, 25164.00, NULL, true, '2022-06-30 07:31:55+00', '2022-06-30 07:31:55+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (42, 10, 1, 3, NULL, 8, NULL, 'rerum', 582, false, NULL, NULL, 98116.00, NULL, true, '2022-06-30 07:31:55+00', '2022-06-30 07:31:55+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (43, 10, 1, NULL, NULL, 5, NULL, 'ipsam', 34, false, NULL, NULL, 89944.00, NULL, false, '2022-06-30 07:31:55+00', '2022-06-30 07:31:55+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (44, 11, 1, 4, 1, 5, NULL, 'corporis', 598, false, NULL, NULL, 26703.00, NULL, true, '2022-06-30 07:31:56+00', '2022-06-30 07:31:56+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (45, 11, 1, 5, 2, 1, NULL, 'consequatur', 816, false, NULL, NULL, 81709.00, NULL, true, '2022-06-30 07:31:56+00', '2022-06-30 07:31:56+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (46, 11, 1, 6, 3, 3, NULL, 'sunt', 315, false, NULL, NULL, 67879.00, NULL, true, '2022-06-30 07:31:56+00', '2022-06-30 07:31:56+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (47, 11, 1, NULL, NULL, 6, NULL, 'ea', 790, false, NULL, NULL, 30444.00, NULL, false, '2022-06-30 07:31:56+00', '2022-06-30 07:31:56+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (48, 12, 1, 4, 1, 9, NULL, 'distinctio', 422, false, NULL, NULL, 99945.00, NULL, true, '2022-06-30 07:31:56+00', '2022-06-30 07:31:56+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (49, 12, 1, 5, 2, 9, NULL, 'molestiae', 951, false, NULL, NULL, 30875.00, NULL, true, '2022-06-30 07:31:56+00', '2022-06-30 07:31:56+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (50, 12, 1, 6, 3, 4, NULL, 'quidem', 829, false, NULL, NULL, 37509.00, NULL, true, '2022-06-30 07:31:56+00', '2022-06-30 07:31:56+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (51, 12, 1, NULL, NULL, 4, NULL, 'aut', 349, false, NULL, NULL, 58332.00, NULL, false, '2022-06-30 07:31:56+00', '2022-06-30 07:31:56+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (52, 13, 1, 4, 1, 5, NULL, 'maiores', 904, false, NULL, NULL, 15659.00, NULL, true, '2022-06-30 07:31:56+00', '2022-06-30 07:31:56+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (53, 13, 1, 5, 2, 1, NULL, 'natus', 729, false, NULL, NULL, 72673.00, NULL, true, '2022-06-30 07:31:56+00', '2022-06-30 07:31:56+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (54, 13, 1, 6, 3, 7, NULL, 'in', 519, false, NULL, NULL, 24104.00, NULL, true, '2022-06-30 07:31:56+00', '2022-06-30 07:31:56+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (55, 13, 1, NULL, NULL, 6, NULL, 'dolor', 617, false, NULL, NULL, 44009.00, NULL, false, '2022-06-30 07:31:56+00', '2022-06-30 07:31:56+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (56, 14, 1, 1, 4, 8, NULL, 'voluptatem', 749, false, NULL, NULL, 6954.00, NULL, true, '2022-06-30 07:31:56+00', '2022-06-30 07:31:56+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (57, 14, 1, 2, 5, 3, NULL, 'est', 787, false, NULL, NULL, 74700.00, NULL, true, '2022-06-30 07:31:56+00', '2022-06-30 07:31:56+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (58, 14, 1, 3, 6, 6, NULL, 'ut', 583, false, NULL, NULL, 76413.00, NULL, true, '2022-06-30 07:31:56+00', '2022-06-30 07:31:56+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (59, 14, 1, NULL, NULL, 8, NULL, 'enim', 563, false, NULL, NULL, 70298.00, NULL, false, '2022-06-30 07:31:56+00', '2022-06-30 07:31:56+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (60, 15, 1, 4, NULL, 5, NULL, 'deserunt', 994, false, NULL, NULL, 67652.00, NULL, true, '2022-06-30 07:31:56+00', '2022-06-30 07:31:56+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (61, 15, 1, 5, NULL, 2, NULL, 'accusantium', 684, false, NULL, NULL, 20058.00, NULL, true, '2022-06-30 07:31:56+00', '2022-06-30 07:31:56+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (62, 15, 1, 6, NULL, 1, NULL, 'tenetur', 498, false, NULL, NULL, 22951.00, NULL, true, '2022-06-30 07:31:56+00', '2022-06-30 07:31:56+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (63, 15, 1, NULL, NULL, 6, NULL, 'deserunt', 411, false, NULL, NULL, 9038.00, NULL, false, '2022-06-30 07:31:56+00', '2022-06-30 07:31:56+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (64, 16, 1, 4, 1, 6, NULL, 'nisi', 844, false, NULL, NULL, 34573.00, NULL, true, '2022-06-30 07:31:56+00', '2022-06-30 07:31:56+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (65, 16, 1, 5, 2, 1, NULL, 'enim', 376, false, NULL, NULL, 10332.00, NULL, true, '2022-06-30 07:31:56+00', '2022-06-30 07:31:56+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (66, 16, 1, 6, 3, 9, NULL, 'consectetur', 701, false, NULL, NULL, 22050.00, NULL, true, '2022-06-30 07:31:56+00', '2022-06-30 07:31:56+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (67, 16, 1, NULL, NULL, 9, NULL, 'ea', 321, false, NULL, NULL, 16610.00, NULL, false, '2022-06-30 07:31:56+00', '2022-06-30 07:31:56+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (68, 17, 1, 4, NULL, 3, NULL, 'sed', 930, false, NULL, NULL, 79385.00, NULL, true, '2022-06-30 07:31:57+00', '2022-06-30 07:31:57+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (69, 17, 1, 5, NULL, 1, NULL, 'qui', 413, false, NULL, NULL, 83161.00, NULL, true, '2022-06-30 07:31:57+00', '2022-06-30 07:31:57+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (70, 17, 1, 6, NULL, 8, NULL, 'molestiae', 919, false, NULL, NULL, 44132.00, NULL, true, '2022-06-30 07:31:57+00', '2022-06-30 07:31:57+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (71, 17, 1, NULL, NULL, 8, NULL, 'voluptate', 872, false, NULL, NULL, 49062.00, NULL, false, '2022-06-30 07:31:57+00', '2022-06-30 07:31:57+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (72, 18, 1, 1, NULL, 3, NULL, 'quae', 779, false, NULL, NULL, 89126.00, NULL, true, '2022-06-30 07:31:57+00', '2022-06-30 07:31:57+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (73, 18, 1, 2, NULL, 6, NULL, 'rerum', 877, false, NULL, NULL, 49649.00, NULL, true, '2022-06-30 07:31:57+00', '2022-06-30 07:31:57+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (74, 18, 1, 3, NULL, 6, NULL, 'saepe', 275, false, NULL, NULL, 76908.00, NULL, true, '2022-06-30 07:31:57+00', '2022-06-30 07:31:57+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (75, 18, 1, NULL, NULL, 6, NULL, 'temporibus', 667, false, NULL, NULL, 89122.00, NULL, false, '2022-06-30 07:31:57+00', '2022-06-30 07:31:57+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (76, 19, 1, 1, 4, 3, NULL, 'ipsam', 759, false, NULL, NULL, 10463.00, NULL, true, '2022-06-30 07:31:57+00', '2022-06-30 07:31:57+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (77, 19, 1, 2, 5, 1, NULL, 'rerum', 682, false, NULL, NULL, 18410.00, NULL, true, '2022-06-30 07:31:57+00', '2022-06-30 07:31:57+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (78, 19, 1, 3, 6, 3, NULL, 'voluptates', 250, false, NULL, NULL, 68460.00, NULL, true, '2022-06-30 07:31:57+00', '2022-06-30 07:31:57+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (79, 19, 1, NULL, NULL, 6, NULL, 'placeat', 526, false, NULL, NULL, 67221.00, NULL, false, '2022-06-30 07:31:57+00', '2022-06-30 07:31:57+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (80, 20, 1, 1, 4, 4, NULL, 'impedit', 421, false, NULL, NULL, 40236.00, NULL, true, '2022-06-30 07:31:57+00', '2022-06-30 07:31:57+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (81, 20, 1, 2, 5, 7, NULL, 'non', 881, false, NULL, NULL, 41945.00, NULL, true, '2022-06-30 07:31:57+00', '2022-06-30 07:31:57+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (82, 20, 1, 3, 6, 4, NULL, 'amet', 824, false, NULL, NULL, 93195.00, NULL, true, '2022-06-30 07:31:57+00', '2022-06-30 07:31:57+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (83, 20, 1, NULL, NULL, 8, NULL, 'sapiente', 766, false, NULL, NULL, 14331.00, NULL, false, '2022-06-30 07:31:57+00', '2022-06-30 07:31:57+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (84, 21, 1, 1, NULL, 1, NULL, 'omnis', 249, false, NULL, NULL, 83527.00, NULL, true, '2022-06-30 07:31:57+00', '2022-06-30 07:31:57+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (85, 21, 1, 2, NULL, 7, NULL, 'nemo', 784, false, NULL, NULL, 43707.00, NULL, true, '2022-06-30 07:31:57+00', '2022-06-30 07:31:57+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (86, 21, 1, 3, NULL, 2, NULL, 'autem', 687, false, NULL, NULL, 39843.00, NULL, true, '2022-06-30 07:31:57+00', '2022-06-30 07:31:57+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (87, 21, 1, NULL, NULL, 5, NULL, 'ut', 697, false, NULL, NULL, 25687.00, NULL, false, '2022-06-30 07:31:57+00', '2022-06-30 07:31:57+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (88, 22, 1, 4, 1, 3, NULL, 'sit', 213, false, NULL, NULL, 11665.00, NULL, true, '2022-06-30 07:31:58+00', '2022-06-30 07:31:58+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (89, 22, 1, 5, 2, 6, NULL, 'quod', 998, false, NULL, NULL, 30618.00, NULL, true, '2022-06-30 07:31:58+00', '2022-06-30 07:31:58+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (90, 22, 1, 6, 3, 8, NULL, 'maxime', 155, false, NULL, NULL, 15818.00, NULL, true, '2022-06-30 07:31:58+00', '2022-06-30 07:31:58+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (91, 22, 1, NULL, NULL, 5, NULL, 'quidem', 2, false, NULL, NULL, 59593.00, NULL, false, '2022-06-30 07:31:58+00', '2022-06-30 07:31:58+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (92, 23, 1, 4, 1, 2, NULL, 'temporibus', 983, false, NULL, NULL, 41689.00, NULL, true, '2022-06-30 07:31:58+00', '2022-06-30 07:31:58+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (93, 23, 1, 5, 2, 5, NULL, 'porro', 375, false, NULL, NULL, 51577.00, NULL, true, '2022-06-30 07:31:58+00', '2022-06-30 07:31:58+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (94, 23, 1, 6, 3, 2, NULL, 'mollitia', 268, false, NULL, NULL, 64699.00, NULL, true, '2022-06-30 07:31:58+00', '2022-06-30 07:31:58+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (95, 23, 1, NULL, NULL, 3, NULL, 'consequuntur', 827, false, NULL, NULL, 80195.00, NULL, false, '2022-06-30 07:31:58+00', '2022-06-30 07:31:58+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (96, 24, 1, 4, NULL, 5, NULL, 'dolore', 870, false, NULL, NULL, 88298.00, NULL, true, '2022-06-30 07:31:58+00', '2022-06-30 07:31:58+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (97, 24, 1, 5, NULL, 1, NULL, 'quis', 104, false, NULL, NULL, 43379.00, NULL, true, '2022-06-30 07:31:58+00', '2022-06-30 07:31:58+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (98, 24, 1, 6, NULL, 5, NULL, 'reprehenderit', 657, false, NULL, NULL, 6839.00, NULL, true, '2022-06-30 07:31:58+00', '2022-06-30 07:31:58+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (99, 24, 1, NULL, NULL, 9, NULL, 'eaque', 378, false, NULL, NULL, 83214.00, NULL, false, '2022-06-30 07:31:58+00', '2022-06-30 07:31:58+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (100, 25, 1, 4, NULL, 7, NULL, 'voluptates', 138, false, NULL, NULL, 97882.00, NULL, true, '2022-06-30 07:31:58+00', '2022-06-30 07:31:58+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (101, 25, 1, 5, NULL, 5, NULL, 'nam', 250, false, NULL, NULL, 49490.00, NULL, true, '2022-06-30 07:31:58+00', '2022-06-30 07:31:58+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (102, 25, 1, 6, NULL, 9, NULL, 'et', 568, false, NULL, NULL, 44801.00, NULL, true, '2022-06-30 07:31:58+00', '2022-06-30 07:31:58+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (103, 25, 1, NULL, NULL, 4, NULL, 'aut', 699, false, NULL, NULL, 2718.00, NULL, false, '2022-06-30 07:31:58+00', '2022-06-30 07:31:58+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (104, 26, 1, 4, NULL, 3, NULL, 'quia', 497, false, NULL, NULL, 70254.00, NULL, true, '2022-06-30 07:31:59+00', '2022-06-30 07:31:59+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (105, 26, 1, 5, NULL, 7, NULL, 'voluptatem', 848, false, NULL, NULL, 51305.00, NULL, true, '2022-06-30 07:31:59+00', '2022-06-30 07:31:59+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (106, 26, 1, 6, NULL, 6, NULL, 'inventore', 811, false, NULL, NULL, 63160.00, NULL, true, '2022-06-30 07:31:59+00', '2022-06-30 07:31:59+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (107, 26, 1, NULL, NULL, 2, NULL, 'labore', 816, false, NULL, NULL, 2949.00, NULL, false, '2022-06-30 07:31:59+00', '2022-06-30 07:31:59+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (108, 27, 1, 1, NULL, 5, NULL, 'ratione', 578, false, NULL, NULL, 82184.00, NULL, true, '2022-06-30 07:31:59+00', '2022-06-30 07:31:59+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (109, 27, 1, 2, NULL, 7, NULL, 'veniam', 631, false, NULL, NULL, 48330.00, NULL, true, '2022-06-30 07:31:59+00', '2022-06-30 07:31:59+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (110, 27, 1, 3, NULL, 7, NULL, 'velit', 839, false, NULL, NULL, 57263.00, NULL, true, '2022-06-30 07:31:59+00', '2022-06-30 07:31:59+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (111, 27, 1, NULL, NULL, 6, NULL, 'explicabo', 218, false, NULL, NULL, 69462.00, NULL, false, '2022-06-30 07:31:59+00', '2022-06-30 07:31:59+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (112, 28, 1, 4, 1, 3, NULL, 'perferendis', 491, false, NULL, NULL, 2666.00, NULL, true, '2022-06-30 07:31:59+00', '2022-06-30 07:31:59+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (113, 28, 1, 5, 2, 9, NULL, 'veritatis', 529, false, NULL, NULL, 69088.00, NULL, true, '2022-06-30 07:31:59+00', '2022-06-30 07:31:59+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (114, 28, 1, 6, 3, 8, NULL, 'a', 160, false, NULL, NULL, 76957.00, NULL, true, '2022-06-30 07:31:59+00', '2022-06-30 07:31:59+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (115, 28, 1, NULL, NULL, 7, NULL, 'reprehenderit', 282, false, NULL, NULL, 17564.00, NULL, false, '2022-06-30 07:31:59+00', '2022-06-30 07:31:59+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (116, 29, 1, 4, NULL, 7, NULL, 'natus', 596, false, NULL, NULL, 63779.00, NULL, true, '2022-06-30 07:31:59+00', '2022-06-30 07:31:59+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (117, 29, 1, 5, NULL, 6, NULL, 'dolorum', 196, false, NULL, NULL, 45599.00, NULL, true, '2022-06-30 07:31:59+00', '2022-06-30 07:31:59+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (118, 29, 1, 6, NULL, 6, NULL, 'consequatur', 457, false, NULL, NULL, 27433.00, NULL, true, '2022-06-30 07:31:59+00', '2022-06-30 07:31:59+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (119, 29, 1, NULL, NULL, 8, NULL, 'quo', 160, false, NULL, NULL, 45157.00, NULL, false, '2022-06-30 07:31:59+00', '2022-06-30 07:31:59+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (120, 30, 1, 4, NULL, 7, NULL, 'qui', 760, false, NULL, NULL, 4090.00, NULL, true, '2022-06-30 07:32:00+00', '2022-06-30 07:32:00+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (121, 30, 1, 5, NULL, 7, NULL, 'consequatur', 463, false, NULL, NULL, 73414.00, NULL, true, '2022-06-30 07:32:00+00', '2022-06-30 07:32:00+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (122, 30, 1, 6, NULL, 3, NULL, 'enim', 768, false, NULL, NULL, 82446.00, NULL, true, '2022-06-30 07:32:00+00', '2022-06-30 07:32:00+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (123, 30, 1, NULL, NULL, 3, NULL, 'blanditiis', 424, false, NULL, NULL, 19114.00, NULL, false, '2022-06-30 07:32:00+00', '2022-06-30 07:32:00+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (124, 31, 1, 1, NULL, 6, NULL, 'voluptatem', 532, false, NULL, NULL, 35418.00, NULL, true, '2022-06-30 07:32:00+00', '2022-06-30 07:32:00+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (125, 31, 1, 2, NULL, 3, NULL, 'non', 479, false, NULL, NULL, 40691.00, NULL, true, '2022-06-30 07:32:00+00', '2022-06-30 07:32:00+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (126, 31, 1, 3, NULL, 3, NULL, 'maiores', 913, false, NULL, NULL, 4616.00, NULL, true, '2022-06-30 07:32:00+00', '2022-06-30 07:32:00+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (127, 31, 1, NULL, NULL, 2, NULL, 'est', 420, false, NULL, NULL, 50195.00, NULL, false, '2022-06-30 07:32:00+00', '2022-06-30 07:32:00+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (128, 32, 1, 4, NULL, 4, NULL, 'natus', 535, false, NULL, NULL, 65266.00, NULL, true, '2022-06-30 07:32:00+00', '2022-06-30 07:32:00+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (129, 32, 1, 5, NULL, 6, NULL, 'dolorem', 839, false, NULL, NULL, 720.00, NULL, true, '2022-06-30 07:32:00+00', '2022-06-30 07:32:00+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (130, 32, 1, 6, NULL, 3, NULL, 'deserunt', 370, false, NULL, NULL, 99856.00, NULL, true, '2022-06-30 07:32:00+00', '2022-06-30 07:32:00+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (131, 32, 1, NULL, NULL, 4, NULL, 'in', 397, false, NULL, NULL, 84297.00, NULL, false, '2022-06-30 07:32:00+00', '2022-06-30 07:32:00+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (132, 33, 1, 1, 4, 1, NULL, 'autem', 755, false, NULL, NULL, 99354.00, NULL, true, '2022-06-30 07:32:00+00', '2022-06-30 07:32:00+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (133, 33, 1, 2, 5, 3, NULL, 'amet', 506, false, NULL, NULL, 86297.00, NULL, true, '2022-06-30 07:32:00+00', '2022-06-30 07:32:00+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (134, 33, 1, 3, 6, 6, NULL, 'itaque', 793, false, NULL, NULL, 40255.00, NULL, true, '2022-06-30 07:32:00+00', '2022-06-30 07:32:00+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (135, 33, 1, NULL, NULL, 9, NULL, 'at', 290, false, NULL, NULL, 82227.00, NULL, false, '2022-06-30 07:32:00+00', '2022-06-30 07:32:00+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (136, 34, 1, 1, NULL, 6, NULL, 'commodi', 778, false, NULL, NULL, 50527.00, NULL, true, '2022-06-30 07:32:01+00', '2022-06-30 07:32:01+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (137, 34, 1, 2, NULL, 6, NULL, 'adipisci', 178, false, NULL, NULL, 79987.00, NULL, true, '2022-06-30 07:32:01+00', '2022-06-30 07:32:01+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (138, 34, 1, 3, NULL, 1, NULL, 'ratione', 638, false, NULL, NULL, 38253.00, NULL, true, '2022-06-30 07:32:01+00', '2022-06-30 07:32:01+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (139, 34, 1, NULL, NULL, 4, NULL, 'non', 911, false, NULL, NULL, 65840.00, NULL, false, '2022-06-30 07:32:01+00', '2022-06-30 07:32:01+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (140, 35, 1, 4, NULL, 3, NULL, 'ad', 830, false, NULL, NULL, 40473.00, NULL, true, '2022-06-30 07:32:01+00', '2022-06-30 07:32:01+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (141, 35, 1, 5, NULL, 9, NULL, 'similique', 782, false, NULL, NULL, 44026.00, NULL, true, '2022-06-30 07:32:01+00', '2022-06-30 07:32:01+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (142, 35, 1, 6, NULL, 8, NULL, 'itaque', 616, false, NULL, NULL, 1448.00, NULL, true, '2022-06-30 07:32:01+00', '2022-06-30 07:32:01+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (143, 35, 1, NULL, NULL, 4, NULL, 'ut', 372, false, NULL, NULL, 28561.00, NULL, false, '2022-06-30 07:32:01+00', '2022-06-30 07:32:01+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (144, 36, 1, 1, 4, 4, NULL, 'odio', 335, false, NULL, NULL, 23251.00, NULL, true, '2022-06-30 07:32:01+00', '2022-06-30 07:32:01+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (145, 36, 1, 2, 5, 2, NULL, 'modi', 813, false, NULL, NULL, 34529.00, NULL, true, '2022-06-30 07:32:01+00', '2022-06-30 07:32:01+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (146, 36, 1, 3, 6, 7, NULL, 'reprehenderit', 566, false, NULL, NULL, 15015.00, NULL, true, '2022-06-30 07:32:01+00', '2022-06-30 07:32:01+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (147, 36, 1, NULL, NULL, 2, NULL, 'rem', 96, false, NULL, NULL, 9241.00, NULL, false, '2022-06-30 07:32:01+00', '2022-06-30 07:32:01+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (148, 37, 1, 4, 1, 9, NULL, 'dolores', 203, false, NULL, NULL, 29453.00, NULL, true, '2022-06-30 07:32:01+00', '2022-06-30 07:32:01+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (149, 37, 1, 5, 2, 9, NULL, 'dolores', 329, false, NULL, NULL, 23594.00, NULL, true, '2022-06-30 07:32:02+00', '2022-06-30 07:32:02+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (150, 37, 1, 6, 3, 4, NULL, 'laboriosam', 259, false, NULL, NULL, 18981.00, NULL, true, '2022-06-30 07:32:02+00', '2022-06-30 07:32:02+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (151, 37, 1, NULL, NULL, 4, NULL, 'corporis', 527, false, NULL, NULL, 24053.00, NULL, false, '2022-06-30 07:32:02+00', '2022-06-30 07:32:02+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (152, 38, 1, 4, 1, 1, NULL, 'tempore', 303, false, NULL, NULL, 21471.00, NULL, true, '2022-06-30 07:32:02+00', '2022-06-30 07:32:02+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (153, 38, 1, 5, 2, 2, NULL, 'molestiae', 317, false, NULL, NULL, 44550.00, NULL, true, '2022-06-30 07:32:02+00', '2022-06-30 07:32:02+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (154, 38, 1, 6, 3, 6, NULL, 'reiciendis', 297, false, NULL, NULL, 60521.00, NULL, true, '2022-06-30 07:32:02+00', '2022-06-30 07:32:02+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (155, 38, 1, NULL, NULL, 4, NULL, 'dolorem', 147, false, NULL, NULL, 41490.00, NULL, false, '2022-06-30 07:32:02+00', '2022-06-30 07:32:02+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (156, 39, 1, 1, NULL, 1, NULL, 'aut', 738, false, NULL, NULL, 1605.00, NULL, true, '2022-06-30 07:32:02+00', '2022-06-30 07:32:02+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (157, 39, 1, 2, NULL, 4, NULL, 'et', 403, false, NULL, NULL, 71543.00, NULL, true, '2022-06-30 07:32:02+00', '2022-06-30 07:32:02+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (158, 39, 1, 3, NULL, 1, NULL, 'impedit', 732, false, NULL, NULL, 47087.00, NULL, true, '2022-06-30 07:32:02+00', '2022-06-30 07:32:02+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (159, 39, 1, NULL, NULL, 3, NULL, 'laborum', 769, false, NULL, NULL, 6054.00, NULL, false, '2022-06-30 07:32:02+00', '2022-06-30 07:32:02+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (160, 40, 1, 4, 1, 6, NULL, 'hic', 683, false, NULL, NULL, 68434.00, NULL, true, '2022-06-30 07:32:02+00', '2022-06-30 07:32:02+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (161, 40, 1, 5, 2, 1, NULL, 'ut', 181, false, NULL, NULL, 295.00, NULL, true, '2022-06-30 07:32:02+00', '2022-06-30 07:32:02+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (162, 40, 1, 6, 3, 1, NULL, 'et', 171, false, NULL, NULL, 72352.00, NULL, true, '2022-06-30 07:32:02+00', '2022-06-30 07:32:02+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (163, 40, 1, NULL, NULL, 1, NULL, 'et', 749, false, NULL, NULL, 49699.00, NULL, false, '2022-06-30 07:32:02+00', '2022-06-30 07:32:02+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (164, 41, 1, 4, NULL, 7, NULL, 'ex', 252, false, NULL, NULL, 49687.00, NULL, true, '2022-06-30 07:32:03+00', '2022-06-30 07:32:03+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (165, 41, 1, 5, NULL, 1, NULL, 'culpa', 894, false, NULL, NULL, 90395.00, NULL, true, '2022-06-30 07:32:03+00', '2022-06-30 07:32:03+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (166, 41, 1, 6, NULL, 6, NULL, 'vel', 390, false, NULL, NULL, 50512.00, NULL, true, '2022-06-30 07:32:03+00', '2022-06-30 07:32:03+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (167, 41, 1, NULL, NULL, 6, NULL, 'excepturi', 904, false, NULL, NULL, 93544.00, NULL, false, '2022-06-30 07:32:03+00', '2022-06-30 07:32:03+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (168, 42, 1, 4, NULL, 7, NULL, 'libero', 641, false, NULL, NULL, 42064.00, NULL, true, '2022-06-30 07:32:03+00', '2022-06-30 07:32:03+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (169, 42, 1, 5, NULL, 6, NULL, 'natus', 926, false, NULL, NULL, 95778.00, NULL, true, '2022-06-30 07:32:03+00', '2022-06-30 07:32:03+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (170, 42, 1, 6, NULL, 3, NULL, 'amet', 408, false, NULL, NULL, 48388.00, NULL, true, '2022-06-30 07:32:03+00', '2022-06-30 07:32:03+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (171, 42, 1, NULL, NULL, 2, NULL, 'dolores', 35, false, NULL, NULL, 11734.00, NULL, false, '2022-06-30 07:32:03+00', '2022-06-30 07:32:03+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (172, 43, 1, 4, 1, 6, NULL, 'iure', 235, false, NULL, NULL, 6973.00, NULL, true, '2022-06-30 07:32:03+00', '2022-06-30 07:32:03+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (173, 43, 1, 5, 2, 1, NULL, 'deleniti', 738, false, NULL, NULL, 33291.00, NULL, true, '2022-06-30 07:32:03+00', '2022-06-30 07:32:03+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (174, 43, 1, 6, 3, 2, NULL, 'officiis', 507, false, NULL, NULL, 45263.00, NULL, true, '2022-06-30 07:32:03+00', '2022-06-30 07:32:03+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (175, 43, 1, NULL, NULL, 1, NULL, 'maiores', 10, false, NULL, NULL, 61742.00, NULL, false, '2022-06-30 07:32:03+00', '2022-06-30 07:32:03+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (176, 44, 1, 1, 4, 1, NULL, 'non', 421, false, NULL, NULL, 51088.00, NULL, true, '2022-06-30 07:32:04+00', '2022-06-30 07:32:04+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (177, 44, 1, 2, 5, 6, NULL, 'adipisci', 706, false, NULL, NULL, 51975.00, NULL, true, '2022-06-30 07:32:04+00', '2022-06-30 07:32:04+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (178, 44, 1, 3, 6, 3, NULL, 'aut', 809, false, NULL, NULL, 43979.00, NULL, true, '2022-06-30 07:32:04+00', '2022-06-30 07:32:04+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (179, 44, 1, NULL, NULL, 9, NULL, 'id', 772, false, NULL, NULL, 150.00, NULL, false, '2022-06-30 07:32:04+00', '2022-06-30 07:32:04+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (180, 45, 1, 4, NULL, 1, NULL, 'delectus', 818, false, NULL, NULL, 73842.00, NULL, true, '2022-06-30 07:32:04+00', '2022-06-30 07:32:04+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (181, 45, 1, 5, NULL, 9, NULL, 'eveniet', 552, false, NULL, NULL, 98381.00, NULL, true, '2022-06-30 07:32:04+00', '2022-06-30 07:32:04+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (182, 45, 1, 6, NULL, 6, NULL, 'ut', 599, false, NULL, NULL, 52495.00, NULL, true, '2022-06-30 07:32:04+00', '2022-06-30 07:32:04+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (183, 45, 1, NULL, NULL, 2, NULL, 'itaque', 629, false, NULL, NULL, 30995.00, NULL, false, '2022-06-30 07:32:04+00', '2022-06-30 07:32:04+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (184, 46, 1, 4, NULL, 9, NULL, 'dolore', 683, false, NULL, NULL, 8200.00, NULL, true, '2022-06-30 07:32:04+00', '2022-06-30 07:32:04+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (185, 46, 1, 5, NULL, 2, NULL, 'et', 507, false, NULL, NULL, 6398.00, NULL, true, '2022-06-30 07:32:04+00', '2022-06-30 07:32:04+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (186, 46, 1, 6, NULL, 4, NULL, 'debitis', 391, false, NULL, NULL, 47160.00, NULL, true, '2022-06-30 07:32:04+00', '2022-06-30 07:32:04+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (187, 46, 1, NULL, NULL, 4, NULL, 'officiis', 650, false, NULL, NULL, 86335.00, NULL, false, '2022-06-30 07:32:04+00', '2022-06-30 07:32:04+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (188, 47, 1, 1, 4, 1, NULL, 'molestias', 837, false, NULL, NULL, 52993.00, NULL, true, '2022-06-30 07:32:05+00', '2022-06-30 07:32:05+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (189, 47, 1, 2, 5, 9, NULL, 'quis', 915, false, NULL, NULL, 97329.00, NULL, true, '2022-06-30 07:32:05+00', '2022-06-30 07:32:05+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (190, 47, 1, 3, 6, 4, NULL, 'aut', 516, false, NULL, NULL, 35720.00, NULL, true, '2022-06-30 07:32:05+00', '2022-06-30 07:32:05+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (191, 47, 1, NULL, NULL, 9, NULL, 'eos', 122, false, NULL, NULL, 31157.00, NULL, false, '2022-06-30 07:32:05+00', '2022-06-30 07:32:05+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (192, 48, 1, 1, 4, 8, NULL, 'quia', 260, false, NULL, NULL, 64614.00, NULL, true, '2022-06-30 07:32:05+00', '2022-06-30 07:32:05+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (193, 48, 1, 2, 5, 1, NULL, 'eos', 351, false, NULL, NULL, 19672.00, NULL, true, '2022-06-30 07:32:05+00', '2022-06-30 07:32:05+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (194, 48, 1, 3, 6, 2, NULL, 'soluta', 387, false, NULL, NULL, 53988.00, NULL, true, '2022-06-30 07:32:05+00', '2022-06-30 07:32:05+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (195, 48, 1, NULL, NULL, 2, NULL, 'et', 508, false, NULL, NULL, 6594.00, NULL, false, '2022-06-30 07:32:05+00', '2022-06-30 07:32:05+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (196, 49, 1, 1, NULL, 1, NULL, 'qui', 394, false, NULL, NULL, 4031.00, NULL, true, '2022-06-30 07:32:05+00', '2022-06-30 07:32:05+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (197, 49, 1, 2, NULL, 2, NULL, 'aut', 387, false, NULL, NULL, 22846.00, NULL, true, '2022-06-30 07:32:05+00', '2022-06-30 07:32:05+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (198, 49, 1, 3, NULL, 3, NULL, 'culpa', 765, false, NULL, NULL, 74282.00, NULL, true, '2022-06-30 07:32:06+00', '2022-06-30 07:32:06+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (199, 49, 1, NULL, NULL, 1, NULL, 'impedit', 763, false, NULL, NULL, 21600.00, NULL, false, '2022-06-30 07:32:06+00', '2022-06-30 07:32:06+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (200, 50, 1, 1, 4, 3, NULL, 'voluptatem', 718, false, NULL, NULL, 42844.00, NULL, true, '2022-06-30 07:32:06+00', '2022-06-30 07:32:06+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (201, 50, 1, 2, 5, 4, NULL, 'dicta', 659, false, NULL, NULL, 86930.00, NULL, true, '2022-06-30 07:32:06+00', '2022-06-30 07:32:06+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (202, 50, 1, 3, 6, 3, NULL, 'commodi', 137, false, NULL, NULL, 22842.00, NULL, true, '2022-06-30 07:32:06+00', '2022-06-30 07:32:06+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (203, 50, 1, NULL, NULL, 2, NULL, 'animi', 773, false, NULL, NULL, 74981.00, NULL, false, '2022-06-30 07:32:06+00', '2022-06-30 07:32:06+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (204, 51, 1, 4, 1, 5, NULL, 'voluptas', 836, false, NULL, NULL, 91239.00, NULL, true, '2022-06-30 07:32:06+00', '2022-06-30 07:32:06+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (205, 51, 1, 5, 2, 2, NULL, 'ratione', 808, false, NULL, NULL, 82462.00, NULL, true, '2022-06-30 07:32:06+00', '2022-06-30 07:32:06+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (206, 51, 1, 6, 3, 4, NULL, 'consequatur', 275, false, NULL, NULL, 18196.00, NULL, true, '2022-06-30 07:32:06+00', '2022-06-30 07:32:06+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (207, 51, 1, NULL, NULL, 1, NULL, 'saepe', 764, false, NULL, NULL, 99590.00, NULL, false, '2022-06-30 07:32:06+00', '2022-06-30 07:32:06+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (208, 52, 1, 1, NULL, 8, NULL, 'ut', 849, false, NULL, NULL, 22265.00, NULL, true, '2022-06-30 07:32:07+00', '2022-06-30 07:32:07+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (209, 52, 1, 2, NULL, 4, NULL, 'odio', 380, false, NULL, NULL, 79417.00, NULL, true, '2022-06-30 07:32:07+00', '2022-06-30 07:32:07+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (210, 52, 1, 3, NULL, 2, NULL, 'qui', 285, false, NULL, NULL, 73701.00, NULL, true, '2022-06-30 07:32:07+00', '2022-06-30 07:32:07+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (211, 52, 1, NULL, NULL, 6, NULL, 'dolores', 637, false, NULL, NULL, 62991.00, NULL, false, '2022-06-30 07:32:07+00', '2022-06-30 07:32:07+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (212, 53, 1, 1, NULL, 5, NULL, 'a', 684, false, NULL, NULL, 38601.00, NULL, true, '2022-06-30 07:32:07+00', '2022-06-30 07:32:07+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (213, 53, 1, 2, NULL, 2, NULL, 'dolores', 936, false, NULL, NULL, 1031.00, NULL, true, '2022-06-30 07:32:07+00', '2022-06-30 07:32:07+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (214, 53, 1, 3, NULL, 7, NULL, 'maiores', 594, false, NULL, NULL, 90377.00, NULL, true, '2022-06-30 07:32:07+00', '2022-06-30 07:32:07+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (215, 53, 1, NULL, NULL, 1, NULL, 'nisi', 238, false, NULL, NULL, 31231.00, NULL, false, '2022-06-30 07:32:07+00', '2022-06-30 07:32:07+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (216, 54, 1, 4, 1, 3, NULL, 'molestias', 738, false, NULL, NULL, 12682.00, NULL, true, '2022-06-30 07:32:07+00', '2022-06-30 07:32:07+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (217, 54, 1, 5, 2, 8, NULL, 'cum', 710, false, NULL, NULL, 61428.00, NULL, true, '2022-06-30 07:32:07+00', '2022-06-30 07:32:07+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (218, 54, 1, 6, 3, 4, NULL, 'nihil', 534, false, NULL, NULL, 75746.00, NULL, true, '2022-06-30 07:32:07+00', '2022-06-30 07:32:07+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (219, 54, 1, NULL, NULL, 8, NULL, 'et', 998, false, NULL, NULL, 91761.00, NULL, false, '2022-06-30 07:32:07+00', '2022-06-30 07:32:07+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (220, 55, 1, 1, 4, 6, NULL, 'quia', 784, false, NULL, NULL, 40502.00, NULL, true, '2022-06-30 07:32:08+00', '2022-06-30 07:32:08+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (221, 55, 1, 2, 5, 4, NULL, 'eaque', 954, false, NULL, NULL, 88996.00, NULL, true, '2022-06-30 07:32:08+00', '2022-06-30 07:32:08+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (222, 55, 1, 3, 6, 4, NULL, 'iste', 491, false, NULL, NULL, 56385.00, NULL, true, '2022-06-30 07:32:08+00', '2022-06-30 07:32:08+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (223, 55, 1, NULL, NULL, 3, NULL, 'voluptates', 142, false, NULL, NULL, 619.00, NULL, false, '2022-06-30 07:32:08+00', '2022-06-30 07:32:08+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (224, 56, 1, 1, NULL, 2, NULL, 'rem', 300, false, NULL, NULL, 74625.00, NULL, true, '2022-06-30 07:32:08+00', '2022-06-30 07:32:08+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (225, 56, 1, 2, NULL, 2, NULL, 'dignissimos', 733, false, NULL, NULL, 1367.00, NULL, true, '2022-06-30 07:32:08+00', '2022-06-30 07:32:08+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (226, 56, 1, 3, NULL, 7, NULL, 'et', 843, false, NULL, NULL, 8386.00, NULL, true, '2022-06-30 07:32:08+00', '2022-06-30 07:32:08+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (227, 56, 1, NULL, NULL, 3, NULL, 'qui', 44, false, NULL, NULL, 26405.00, NULL, false, '2022-06-30 07:32:08+00', '2022-06-30 07:32:08+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (228, 57, 1, 1, NULL, 3, NULL, 'ipsum', 814, false, NULL, NULL, 4634.00, NULL, true, '2022-06-30 07:32:09+00', '2022-06-30 07:32:09+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (229, 57, 1, 2, NULL, 9, NULL, 'nihil', 748, false, NULL, NULL, 91855.00, NULL, true, '2022-06-30 07:32:09+00', '2022-06-30 07:32:09+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (230, 57, 1, 3, NULL, 9, NULL, 'molestiae', 329, false, NULL, NULL, 27024.00, NULL, true, '2022-06-30 07:32:09+00', '2022-06-30 07:32:09+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (231, 57, 1, NULL, NULL, 7, NULL, 'velit', 771, false, NULL, NULL, 62898.00, NULL, false, '2022-06-30 07:32:09+00', '2022-06-30 07:32:09+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (232, 58, 1, 1, 4, 6, NULL, 'quas', 516, false, NULL, NULL, 34943.00, NULL, true, '2022-06-30 07:32:09+00', '2022-06-30 07:32:09+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (233, 58, 1, 2, 5, 6, NULL, 'placeat', 940, false, NULL, NULL, 22955.00, NULL, true, '2022-06-30 07:32:09+00', '2022-06-30 07:32:09+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (234, 58, 1, 3, 6, 4, NULL, 'est', 342, false, NULL, NULL, 27093.00, NULL, true, '2022-06-30 07:32:09+00', '2022-06-30 07:32:09+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (235, 58, 1, NULL, NULL, 9, NULL, 'fuga', 738, false, NULL, NULL, 74067.00, NULL, false, '2022-06-30 07:32:09+00', '2022-06-30 07:32:09+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (236, 59, 1, 4, NULL, 3, NULL, 'totam', 257, false, NULL, NULL, 73260.00, NULL, true, '2022-06-30 07:32:09+00', '2022-06-30 07:32:09+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (237, 59, 1, 5, NULL, 2, NULL, 'exercitationem', 891, false, NULL, NULL, 7490.00, NULL, true, '2022-06-30 07:32:09+00', '2022-06-30 07:32:09+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (238, 59, 1, 6, NULL, 2, NULL, 'sint', 297, false, NULL, NULL, 9644.00, NULL, true, '2022-06-30 07:32:09+00', '2022-06-30 07:32:09+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (239, 59, 1, NULL, NULL, 7, NULL, 'fugit', 268, false, NULL, NULL, 16868.00, NULL, false, '2022-06-30 07:32:09+00', '2022-06-30 07:32:09+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (240, 60, 1, 4, NULL, 8, NULL, 'explicabo', 631, false, NULL, NULL, 16720.00, NULL, true, '2022-06-30 07:32:10+00', '2022-06-30 07:32:10+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (241, 60, 1, 5, NULL, 5, NULL, 'soluta', 913, false, NULL, NULL, 44407.00, NULL, true, '2022-06-30 07:32:10+00', '2022-06-30 07:32:10+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (242, 60, 1, 6, NULL, 4, NULL, 'voluptatibus', 501, false, NULL, NULL, 25948.00, NULL, true, '2022-06-30 07:32:10+00', '2022-06-30 07:32:10+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (243, 60, 1, NULL, NULL, 2, NULL, 'nesciunt', 460, false, NULL, NULL, 19263.00, NULL, false, '2022-06-30 07:32:10+00', '2022-06-30 07:32:10+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (244, 61, 1, 1, NULL, 8, NULL, 'ut', 158, false, NULL, NULL, 2746.00, NULL, true, '2022-06-30 07:32:10+00', '2022-06-30 07:32:10+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (245, 61, 1, 2, NULL, 6, NULL, 'dolorum', 433, false, NULL, NULL, 95154.00, NULL, true, '2022-06-30 07:32:10+00', '2022-06-30 07:32:10+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (246, 61, 1, 3, NULL, 1, NULL, 'fugiat', 792, false, NULL, NULL, 46972.00, NULL, true, '2022-06-30 07:32:10+00', '2022-06-30 07:32:10+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (247, 61, 1, NULL, NULL, 8, NULL, 'ipsum', 533, false, NULL, NULL, 29948.00, NULL, false, '2022-06-30 07:32:10+00', '2022-06-30 07:32:10+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (248, 62, 1, 1, NULL, 3, NULL, 'est', 869, false, NULL, NULL, 66941.00, NULL, true, '2022-06-30 07:32:11+00', '2022-06-30 07:32:11+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (249, 62, 1, 2, NULL, 9, NULL, 'dolorem', 922, false, NULL, NULL, 98778.00, NULL, true, '2022-06-30 07:32:11+00', '2022-06-30 07:32:11+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (250, 62, 1, 3, NULL, 5, NULL, 'molestiae', 540, false, NULL, NULL, 80019.00, NULL, true, '2022-06-30 07:32:11+00', '2022-06-30 07:32:11+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (251, 62, 1, NULL, NULL, 4, NULL, 'doloremque', 656, false, NULL, NULL, 57035.00, NULL, false, '2022-06-30 07:32:11+00', '2022-06-30 07:32:11+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (252, 63, 1, 1, NULL, 1, NULL, 'reiciendis', 779, false, NULL, NULL, 82981.00, NULL, true, '2022-06-30 07:32:11+00', '2022-06-30 07:32:11+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (253, 63, 1, 2, NULL, 8, NULL, 'perspiciatis', 178, false, NULL, NULL, 25731.00, NULL, true, '2022-06-30 07:32:11+00', '2022-06-30 07:32:11+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (254, 63, 1, 3, NULL, 1, NULL, 'et', 605, false, NULL, NULL, 58046.00, NULL, true, '2022-06-30 07:32:11+00', '2022-06-30 07:32:11+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (255, 63, 1, NULL, NULL, 4, NULL, 'et', 134, false, NULL, NULL, 8790.00, NULL, false, '2022-06-30 07:32:11+00', '2022-06-30 07:32:11+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (256, 64, 1, 4, 1, 4, NULL, 'eveniet', 239, false, NULL, NULL, 82369.00, NULL, true, '2022-06-30 07:32:11+00', '2022-06-30 07:32:11+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (257, 64, 1, 5, 2, 8, NULL, 'ut', 938, false, NULL, NULL, 27115.00, NULL, true, '2022-06-30 07:32:12+00', '2022-06-30 07:32:12+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (258, 64, 1, 6, 3, 9, NULL, 'eaque', 107, false, NULL, NULL, 66211.00, NULL, true, '2022-06-30 07:32:12+00', '2022-06-30 07:32:12+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (259, 64, 1, NULL, NULL, 6, NULL, 'odit', 462, false, NULL, NULL, 99360.00, NULL, false, '2022-06-30 07:32:12+00', '2022-06-30 07:32:12+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (260, 65, 1, 1, NULL, 3, NULL, 'et', 585, false, NULL, NULL, 76497.00, NULL, true, '2022-06-30 07:32:12+00', '2022-06-30 07:32:12+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (261, 65, 1, 2, NULL, 8, NULL, 'inventore', 352, false, NULL, NULL, 4965.00, NULL, true, '2022-06-30 07:32:12+00', '2022-06-30 07:32:12+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (262, 65, 1, 3, NULL, 1, NULL, 'quas', 554, false, NULL, NULL, 31215.00, NULL, true, '2022-06-30 07:32:12+00', '2022-06-30 07:32:12+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (263, 65, 1, NULL, NULL, 7, NULL, 'temporibus', 456, false, NULL, NULL, 97562.00, NULL, false, '2022-06-30 07:32:12+00', '2022-06-30 07:32:12+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (264, 66, 1, 4, NULL, 8, NULL, 'nobis', 815, false, NULL, NULL, 99094.00, NULL, true, '2022-06-30 07:32:12+00', '2022-06-30 07:32:12+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (265, 66, 1, 5, NULL, 1, NULL, 'repellat', 751, false, NULL, NULL, 62279.00, NULL, true, '2022-06-30 07:32:12+00', '2022-06-30 07:32:12+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (266, 66, 1, 6, NULL, 5, NULL, 'qui', 107, false, NULL, NULL, 99560.00, NULL, true, '2022-06-30 07:32:12+00', '2022-06-30 07:32:12+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (267, 66, 1, NULL, NULL, 5, NULL, 'eos', 129, false, NULL, NULL, 45952.00, NULL, false, '2022-06-30 07:32:13+00', '2022-06-30 07:32:13+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (268, 67, 1, 1, NULL, 9, NULL, 'aperiam', 629, false, NULL, NULL, 34197.00, NULL, true, '2022-06-30 07:32:13+00', '2022-06-30 07:32:13+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (269, 67, 1, 2, NULL, 1, NULL, 'est', 972, false, NULL, NULL, 5393.00, NULL, true, '2022-06-30 07:32:13+00', '2022-06-30 07:32:13+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (270, 67, 1, 3, NULL, 3, NULL, 'totam', 495, false, NULL, NULL, 47491.00, NULL, true, '2022-06-30 07:32:13+00', '2022-06-30 07:32:13+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (271, 67, 1, NULL, NULL, 8, NULL, 'facere', 561, false, NULL, NULL, 64719.00, NULL, false, '2022-06-30 07:32:13+00', '2022-06-30 07:32:13+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (272, 68, 1, 1, NULL, 9, NULL, 'sunt', 113, false, NULL, NULL, 74269.00, NULL, true, '2022-06-30 07:32:13+00', '2022-06-30 07:32:13+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (273, 68, 1, 2, NULL, 7, NULL, 'at', 273, false, NULL, NULL, 7893.00, NULL, true, '2022-06-30 07:32:13+00', '2022-06-30 07:32:13+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (274, 68, 1, 3, NULL, 9, NULL, 'qui', 436, false, NULL, NULL, 47553.00, NULL, true, '2022-06-30 07:32:13+00', '2022-06-30 07:32:13+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (275, 68, 1, NULL, NULL, 7, NULL, 'sequi', 37, false, NULL, NULL, 75185.00, NULL, false, '2022-06-30 07:32:13+00', '2022-06-30 07:32:13+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (276, 69, 1, 1, NULL, 2, NULL, 'et', 159, false, NULL, NULL, 46306.00, NULL, true, '2022-06-30 07:32:14+00', '2022-06-30 07:32:14+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (277, 69, 1, 2, NULL, 3, NULL, 'voluptates', 980, false, NULL, NULL, 53125.00, NULL, true, '2022-06-30 07:32:14+00', '2022-06-30 07:32:14+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (278, 69, 1, 3, NULL, 8, NULL, 'sit', 116, false, NULL, NULL, 41677.00, NULL, true, '2022-06-30 07:32:14+00', '2022-06-30 07:32:14+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (279, 69, 1, NULL, NULL, 3, NULL, 'consequatur', 268, false, NULL, NULL, 49231.00, NULL, false, '2022-06-30 07:32:14+00', '2022-06-30 07:32:14+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (280, 70, 1, 4, NULL, 2, NULL, 'et', 723, false, NULL, NULL, 287.00, NULL, true, '2022-06-30 07:32:14+00', '2022-06-30 07:32:14+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (281, 70, 1, 5, NULL, 7, NULL, 'ea', 778, false, NULL, NULL, 53932.00, NULL, true, '2022-06-30 07:32:14+00', '2022-06-30 07:32:14+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (282, 70, 1, 6, NULL, 4, NULL, 'placeat', 462, false, NULL, NULL, 78512.00, NULL, true, '2022-06-30 07:32:14+00', '2022-06-30 07:32:14+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (283, 70, 1, NULL, NULL, 9, NULL, 'repudiandae', 943, false, NULL, NULL, 71288.00, NULL, false, '2022-06-30 07:32:14+00', '2022-06-30 07:32:14+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (284, 71, 1, 1, NULL, 9, NULL, 'odio', 144, false, NULL, NULL, 39390.00, NULL, true, '2022-06-30 07:32:15+00', '2022-06-30 07:32:15+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (285, 71, 1, 2, NULL, 3, NULL, 'commodi', 632, false, NULL, NULL, 88246.00, NULL, true, '2022-06-30 07:32:15+00', '2022-06-30 07:32:15+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (286, 71, 1, 3, NULL, 3, NULL, 'aut', 472, false, NULL, NULL, 94205.00, NULL, true, '2022-06-30 07:32:15+00', '2022-06-30 07:32:15+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (287, 71, 1, NULL, NULL, 8, NULL, 'molestiae', 754, false, NULL, NULL, 43194.00, NULL, false, '2022-06-30 07:32:15+00', '2022-06-30 07:32:15+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (288, 72, 1, 1, NULL, 9, NULL, 'deserunt', 943, false, NULL, NULL, 86617.00, NULL, true, '2022-06-30 07:32:15+00', '2022-06-30 07:32:15+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (289, 72, 1, 2, NULL, 1, NULL, 'tempora', 810, false, NULL, NULL, 98065.00, NULL, true, '2022-06-30 07:32:15+00', '2022-06-30 07:32:15+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (290, 72, 1, 3, NULL, 5, NULL, 'et', 886, false, NULL, NULL, 1197.00, NULL, true, '2022-06-30 07:32:15+00', '2022-06-30 07:32:15+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (291, 72, 1, NULL, NULL, 1, NULL, 'nulla', 214, false, NULL, NULL, 90661.00, NULL, false, '2022-06-30 07:32:15+00', '2022-06-30 07:32:15+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (292, 73, 1, 4, NULL, 7, NULL, 'sint', 685, false, NULL, NULL, 92441.00, NULL, true, '2022-06-30 07:32:16+00', '2022-06-30 07:32:16+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (293, 73, 1, 5, NULL, 1, NULL, 'doloremque', 837, false, NULL, NULL, 89845.00, NULL, true, '2022-06-30 07:32:16+00', '2022-06-30 07:32:16+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (294, 73, 1, 6, NULL, 3, NULL, 'totam', 966, false, NULL, NULL, 59860.00, NULL, true, '2022-06-30 07:32:16+00', '2022-06-30 07:32:16+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (295, 73, 1, NULL, NULL, 5, NULL, 'consequatur', 130, false, NULL, NULL, 30838.00, NULL, false, '2022-06-30 07:32:16+00', '2022-06-30 07:32:16+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (296, 74, 1, 1, 4, 7, NULL, 'maxime', 690, false, NULL, NULL, 77279.00, NULL, true, '2022-06-30 07:32:16+00', '2022-06-30 07:32:16+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (297, 74, 1, 2, 5, 4, NULL, 'minima', 670, false, NULL, NULL, 21709.00, NULL, true, '2022-06-30 07:32:16+00', '2022-06-30 07:32:16+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (298, 74, 1, 3, 6, 4, NULL, 'voluptas', 434, false, NULL, NULL, 87468.00, NULL, true, '2022-06-30 07:32:16+00', '2022-06-30 07:32:16+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (299, 74, 1, NULL, NULL, 8, NULL, 'ut', 795, false, NULL, NULL, 75864.00, NULL, false, '2022-06-30 07:32:16+00', '2022-06-30 07:32:16+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (300, 75, 1, 4, NULL, 3, NULL, 'ut', 555, false, NULL, NULL, 11146.00, NULL, true, '2022-06-30 07:32:17+00', '2022-06-30 07:32:17+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (301, 75, 1, 5, NULL, 4, NULL, 'recusandae', 973, false, NULL, NULL, 76022.00, NULL, true, '2022-06-30 07:32:17+00', '2022-06-30 07:32:17+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (302, 75, 1, 6, NULL, 9, NULL, 'quidem', 766, false, NULL, NULL, 25884.00, NULL, true, '2022-06-30 07:32:17+00', '2022-06-30 07:32:17+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (303, 75, 1, NULL, NULL, 7, NULL, 'facere', 459, false, NULL, NULL, 73825.00, NULL, false, '2022-06-30 07:32:17+00', '2022-06-30 07:32:17+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (304, 76, 1, 4, NULL, 9, NULL, 'illo', 720, false, NULL, NULL, 54275.00, NULL, true, '2022-06-30 07:32:17+00', '2022-06-30 07:32:17+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (305, 76, 1, 5, NULL, 4, NULL, 'a', 788, false, NULL, NULL, 25906.00, NULL, true, '2022-06-30 07:32:17+00', '2022-06-30 07:32:17+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (306, 76, 1, 6, NULL, 4, NULL, 'blanditiis', 405, false, NULL, NULL, 78118.00, NULL, true, '2022-06-30 07:32:17+00', '2022-06-30 07:32:17+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (307, 76, 1, NULL, NULL, 4, NULL, 'amet', 83, false, NULL, NULL, 37722.00, NULL, false, '2022-06-30 07:32:17+00', '2022-06-30 07:32:17+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (308, 77, 1, 4, 1, 1, NULL, 'soluta', 463, false, NULL, NULL, 61090.00, NULL, true, '2022-06-30 07:32:18+00', '2022-06-30 07:32:18+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (309, 77, 1, 5, 2, 9, NULL, 'deleniti', 624, false, NULL, NULL, 52140.00, NULL, true, '2022-06-30 07:32:18+00', '2022-06-30 07:32:18+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (310, 77, 1, 6, 3, 5, NULL, 'deserunt', 935, false, NULL, NULL, 57043.00, NULL, true, '2022-06-30 07:32:18+00', '2022-06-30 07:32:18+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (311, 77, 1, NULL, NULL, 1, NULL, 'ut', 623, false, NULL, NULL, 42187.00, NULL, false, '2022-06-30 07:32:18+00', '2022-06-30 07:32:18+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (312, 78, 1, 1, NULL, 8, NULL, 'rem', 916, false, NULL, NULL, 54505.00, NULL, true, '2022-06-30 07:32:18+00', '2022-06-30 07:32:18+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (313, 78, 1, 2, NULL, 4, NULL, 'doloribus', 605, false, NULL, NULL, 33369.00, NULL, true, '2022-06-30 07:32:18+00', '2022-06-30 07:32:18+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (314, 78, 1, 3, NULL, 1, NULL, 'odit', 682, false, NULL, NULL, 69815.00, NULL, true, '2022-06-30 07:32:18+00', '2022-06-30 07:32:18+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (315, 78, 1, NULL, NULL, 7, NULL, 'et', 953, false, NULL, NULL, 52014.00, NULL, false, '2022-06-30 07:32:18+00', '2022-06-30 07:32:18+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (316, 79, 1, 1, 4, 4, NULL, 'repudiandae', 878, false, NULL, NULL, 35146.00, NULL, true, '2022-06-30 07:32:19+00', '2022-06-30 07:32:19+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (317, 79, 1, 2, 5, 5, NULL, 'molestiae', 390, false, NULL, NULL, 33011.00, NULL, true, '2022-06-30 07:32:19+00', '2022-06-30 07:32:19+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (318, 79, 1, 3, 6, 8, NULL, 'quasi', 451, false, NULL, NULL, 43062.00, NULL, true, '2022-06-30 07:32:19+00', '2022-06-30 07:32:19+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (319, 79, 1, NULL, NULL, 7, NULL, 'aliquam', 314, false, NULL, NULL, 86674.00, NULL, false, '2022-06-30 07:32:19+00', '2022-06-30 07:32:19+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (320, 80, 1, 1, 4, 5, NULL, 'et', 568, false, NULL, NULL, 64050.00, NULL, true, '2022-06-30 07:32:19+00', '2022-06-30 07:32:19+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (321, 80, 1, 2, 5, 5, NULL, 'quia', 736, false, NULL, NULL, 58293.00, NULL, true, '2022-06-30 07:32:19+00', '2022-06-30 07:32:19+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (322, 80, 1, 3, 6, 5, NULL, 'deserunt', 981, false, NULL, NULL, 27856.00, NULL, true, '2022-06-30 07:32:19+00', '2022-06-30 07:32:19+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (323, 80, 1, NULL, NULL, 4, NULL, 'deleniti', 218, false, NULL, NULL, 14799.00, NULL, false, '2022-06-30 07:32:19+00', '2022-06-30 07:32:19+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (324, 81, 1, 4, 1, 3, NULL, 'nostrum', 309, false, NULL, NULL, 28836.00, NULL, true, '2022-06-30 07:32:20+00', '2022-06-30 07:32:20+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (325, 81, 1, 5, 2, 7, NULL, 'nesciunt', 367, false, NULL, NULL, 58748.00, NULL, true, '2022-06-30 07:32:20+00', '2022-06-30 07:32:20+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (326, 81, 1, 6, 3, 9, NULL, 'et', 130, false, NULL, NULL, 19921.00, NULL, true, '2022-06-30 07:32:20+00', '2022-06-30 07:32:20+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (327, 81, 1, NULL, NULL, 8, NULL, 'quam', 126, false, NULL, NULL, 77792.00, NULL, false, '2022-06-30 07:32:20+00', '2022-06-30 07:32:20+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (328, 82, 1, 4, NULL, 2, NULL, 'ratione', 454, false, NULL, NULL, 5806.00, NULL, true, '2022-06-30 07:32:20+00', '2022-06-30 07:32:20+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (329, 82, 1, 5, NULL, 1, NULL, 'consequatur', 569, false, NULL, NULL, 47660.00, NULL, true, '2022-06-30 07:32:20+00', '2022-06-30 07:32:20+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (330, 82, 1, 6, NULL, 1, NULL, 'voluptatibus', 414, false, NULL, NULL, 49043.00, NULL, true, '2022-06-30 07:32:20+00', '2022-06-30 07:32:20+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (331, 82, 1, NULL, NULL, 1, NULL, 'dignissimos', 702, false, NULL, NULL, 61219.00, NULL, false, '2022-06-30 07:32:20+00', '2022-06-30 07:32:20+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (332, 83, 1, 4, 1, 4, NULL, 'accusantium', 755, false, NULL, NULL, 85880.00, NULL, true, '2022-06-30 07:32:21+00', '2022-06-30 07:32:21+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (333, 83, 1, 5, 2, 2, NULL, 'voluptas', 194, false, NULL, NULL, 46867.00, NULL, true, '2022-06-30 07:32:21+00', '2022-06-30 07:32:21+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (334, 83, 1, 6, 3, 9, NULL, 'quia', 368, false, NULL, NULL, 10453.00, NULL, true, '2022-06-30 07:32:21+00', '2022-06-30 07:32:21+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (335, 83, 1, NULL, NULL, 3, NULL, 'alias', 771, false, NULL, NULL, 84033.00, NULL, false, '2022-06-30 07:32:21+00', '2022-06-30 07:32:21+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (336, 84, 1, 1, NULL, 7, NULL, 'molestiae', 546, false, NULL, NULL, 74416.00, NULL, true, '2022-06-30 07:32:21+00', '2022-06-30 07:32:21+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (337, 84, 1, 2, NULL, 5, NULL, 'qui', 285, false, NULL, NULL, 91052.00, NULL, true, '2022-06-30 07:32:21+00', '2022-06-30 07:32:21+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (338, 84, 1, 3, NULL, 9, NULL, 'perferendis', 731, false, NULL, NULL, 41494.00, NULL, true, '2022-06-30 07:32:22+00', '2022-06-30 07:32:22+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (339, 84, 1, NULL, NULL, 2, NULL, 'atque', 917, false, NULL, NULL, 49780.00, NULL, false, '2022-06-30 07:32:22+00', '2022-06-30 07:32:22+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (340, 85, 1, 1, 4, 4, NULL, 'aliquam', 526, false, NULL, NULL, 94065.00, NULL, true, '2022-06-30 07:32:22+00', '2022-06-30 07:32:22+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (341, 85, 1, 2, 5, 8, NULL, 'quas', 312, false, NULL, NULL, 89954.00, NULL, true, '2022-06-30 07:32:22+00', '2022-06-30 07:32:22+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (342, 85, 1, 3, 6, 7, NULL, 'quis', 113, false, NULL, NULL, 4865.00, NULL, true, '2022-06-30 07:32:22+00', '2022-06-30 07:32:22+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (343, 85, 1, NULL, NULL, 6, NULL, 'eos', 881, false, NULL, NULL, 58293.00, NULL, false, '2022-06-30 07:32:22+00', '2022-06-30 07:32:22+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (344, 86, 1, 1, 4, 8, NULL, 'nostrum', 326, false, NULL, NULL, 96826.00, NULL, true, '2022-06-30 07:32:23+00', '2022-06-30 07:32:23+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (345, 86, 1, 2, 5, 7, NULL, 'omnis', 262, false, NULL, NULL, 87190.00, NULL, true, '2022-06-30 07:32:23+00', '2022-06-30 07:32:23+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (346, 86, 1, 3, 6, 3, NULL, 'tempore', 791, false, NULL, NULL, 30044.00, NULL, true, '2022-06-30 07:32:23+00', '2022-06-30 07:32:23+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (347, 86, 1, NULL, NULL, 3, NULL, 'eveniet', 62, false, NULL, NULL, 78580.00, NULL, false, '2022-06-30 07:32:23+00', '2022-06-30 07:32:23+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (348, 87, 1, 1, NULL, 2, NULL, 'aliquam', 961, false, NULL, NULL, 31015.00, NULL, true, '2022-06-30 07:32:23+00', '2022-06-30 07:32:23+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (349, 87, 1, 2, NULL, 9, NULL, 'voluptatem', 223, false, NULL, NULL, 51240.00, NULL, true, '2022-06-30 07:32:23+00', '2022-06-30 07:32:23+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (350, 87, 1, 3, NULL, 6, NULL, 'doloribus', 589, false, NULL, NULL, 13182.00, NULL, true, '2022-06-30 07:32:23+00', '2022-06-30 07:32:23+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (351, 87, 1, NULL, NULL, 3, NULL, 'quam', 612, false, NULL, NULL, 29046.00, NULL, false, '2022-06-30 07:32:23+00', '2022-06-30 07:32:23+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (352, 88, 1, 1, NULL, 9, NULL, 'deleniti', 466, false, NULL, NULL, 67236.00, NULL, true, '2022-06-30 07:32:24+00', '2022-06-30 07:32:24+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (353, 88, 1, 2, NULL, 8, NULL, 'et', 774, false, NULL, NULL, 69193.00, NULL, true, '2022-06-30 07:32:24+00', '2022-06-30 07:32:24+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (354, 88, 1, 3, NULL, 7, NULL, 'officia', 961, false, NULL, NULL, 25943.00, NULL, true, '2022-06-30 07:32:24+00', '2022-06-30 07:32:24+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (355, 88, 1, NULL, NULL, 2, NULL, 'dolores', 375, false, NULL, NULL, 52304.00, NULL, false, '2022-06-30 07:32:24+00', '2022-06-30 07:32:24+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (356, 89, 1, 1, NULL, 3, NULL, 'accusantium', 794, false, NULL, NULL, 70664.00, NULL, true, '2022-06-30 07:32:24+00', '2022-06-30 07:32:24+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (357, 89, 1, 2, NULL, 9, NULL, 'dolor', 781, false, NULL, NULL, 85741.00, NULL, true, '2022-06-30 07:32:24+00', '2022-06-30 07:32:24+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (358, 89, 1, 3, NULL, 9, NULL, 'impedit', 304, false, NULL, NULL, 34248.00, NULL, true, '2022-06-30 07:32:24+00', '2022-06-30 07:32:24+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (359, 89, 1, NULL, NULL, 2, NULL, 'et', 1, false, NULL, NULL, 72008.00, NULL, false, '2022-06-30 07:32:24+00', '2022-06-30 07:32:24+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (360, 90, 1, 1, 4, 9, NULL, 'est', 515, false, NULL, NULL, 86164.00, NULL, true, '2022-06-30 07:32:25+00', '2022-06-30 07:32:25+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (361, 90, 1, 2, 5, 7, NULL, 'expedita', 468, false, NULL, NULL, 47978.00, NULL, true, '2022-06-30 07:32:25+00', '2022-06-30 07:32:25+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (362, 90, 1, 3, 6, 3, NULL, 'nesciunt', 445, false, NULL, NULL, 89167.00, NULL, true, '2022-06-30 07:32:25+00', '2022-06-30 07:32:25+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (363, 90, 1, NULL, NULL, 1, NULL, 'aperiam', 260, false, NULL, NULL, 95675.00, NULL, false, '2022-06-30 07:32:25+00', '2022-06-30 07:32:25+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (364, 91, 1, 1, NULL, 3, NULL, 'vitae', 671, false, NULL, NULL, 3010.00, NULL, true, '2022-06-30 07:32:25+00', '2022-06-30 07:32:25+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (365, 91, 1, 2, NULL, 9, NULL, 'doloribus', 999, false, NULL, NULL, 65385.00, NULL, true, '2022-06-30 07:32:26+00', '2022-06-30 07:32:26+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (366, 91, 1, 3, NULL, 7, NULL, 'sit', 719, false, NULL, NULL, 80193.00, NULL, true, '2022-06-30 07:32:26+00', '2022-06-30 07:32:26+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (367, 91, 1, NULL, NULL, 7, NULL, 'dignissimos', 105, false, NULL, NULL, 23318.00, NULL, false, '2022-06-30 07:32:26+00', '2022-06-30 07:32:26+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (368, 92, 1, 1, 4, 6, NULL, 'eos', 926, false, NULL, NULL, 85552.00, NULL, true, '2022-06-30 07:32:26+00', '2022-06-30 07:32:26+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (369, 92, 1, 2, 5, 6, NULL, 'et', 581, false, NULL, NULL, 8340.00, NULL, true, '2022-06-30 07:32:26+00', '2022-06-30 07:32:26+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (370, 92, 1, 3, 6, 3, NULL, 'perspiciatis', 716, false, NULL, NULL, 20584.00, NULL, true, '2022-06-30 07:32:26+00', '2022-06-30 07:32:26+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (371, 92, 1, NULL, NULL, 9, NULL, 'assumenda', 837, false, NULL, NULL, 48390.00, NULL, false, '2022-06-30 07:32:26+00', '2022-06-30 07:32:26+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (372, 93, 1, 1, 4, 4, NULL, 'inventore', 980, false, NULL, NULL, 4917.00, NULL, true, '2022-06-30 07:32:27+00', '2022-06-30 07:32:27+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (373, 93, 1, 2, 5, 1, NULL, 'sint', 608, false, NULL, NULL, 77109.00, NULL, true, '2022-06-30 07:32:27+00', '2022-06-30 07:32:27+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (374, 93, 1, 3, 6, 7, NULL, 'perferendis', 319, false, NULL, NULL, 67232.00, NULL, true, '2022-06-30 07:32:27+00', '2022-06-30 07:32:27+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (375, 93, 1, NULL, NULL, 4, NULL, 'explicabo', 148, false, NULL, NULL, 69109.00, NULL, false, '2022-06-30 07:32:27+00', '2022-06-30 07:32:27+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (376, 94, 1, 4, NULL, 5, NULL, 'velit', 270, false, NULL, NULL, 50316.00, NULL, true, '2022-06-30 07:32:27+00', '2022-06-30 07:32:27+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (377, 94, 1, 5, NULL, 9, NULL, 'quia', 729, false, NULL, NULL, 17384.00, NULL, true, '2022-06-30 07:32:27+00', '2022-06-30 07:32:27+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (378, 94, 1, 6, NULL, 7, NULL, 'nesciunt', 830, false, NULL, NULL, 4492.00, NULL, true, '2022-06-30 07:32:27+00', '2022-06-30 07:32:27+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (379, 94, 1, NULL, NULL, 9, NULL, 'earum', 689, false, NULL, NULL, 26996.00, NULL, false, '2022-06-30 07:32:27+00', '2022-06-30 07:32:27+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (380, 95, 1, 1, 4, 9, NULL, 'quod', 660, false, NULL, NULL, 77173.00, NULL, true, '2022-06-30 07:32:28+00', '2022-06-30 07:32:28+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (381, 95, 1, 2, 5, 2, NULL, 'excepturi', 916, false, NULL, NULL, 27990.00, NULL, true, '2022-06-30 07:32:28+00', '2022-06-30 07:32:28+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (382, 95, 1, 3, 6, 1, NULL, 'debitis', 456, false, NULL, NULL, 19254.00, NULL, true, '2022-06-30 07:32:28+00', '2022-06-30 07:32:28+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (383, 95, 1, NULL, NULL, 7, NULL, 'aut', 525, false, NULL, NULL, 72221.00, NULL, false, '2022-06-30 07:32:28+00', '2022-06-30 07:32:28+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (384, 96, 1, 4, NULL, 6, NULL, 'aut', 647, false, NULL, NULL, 63148.00, NULL, true, '2022-06-30 07:32:29+00', '2022-06-30 07:32:29+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (385, 96, 1, 5, NULL, 8, NULL, 'facilis', 557, false, NULL, NULL, 67118.00, NULL, true, '2022-06-30 07:32:29+00', '2022-06-30 07:32:29+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (386, 96, 1, 6, NULL, 6, NULL, 'ab', 687, false, NULL, NULL, 63441.00, NULL, true, '2022-06-30 07:32:29+00', '2022-06-30 07:32:29+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (387, 96, 1, NULL, NULL, 5, NULL, 'vel', 225, false, NULL, NULL, 43987.00, NULL, false, '2022-06-30 07:32:29+00', '2022-06-30 07:32:29+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (388, 97, 1, 4, 1, 2, NULL, 'id', 824, false, NULL, NULL, 59702.00, NULL, true, '2022-06-30 07:32:29+00', '2022-06-30 07:32:29+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (389, 97, 1, 5, 2, 8, NULL, 'quae', 721, false, NULL, NULL, 50404.00, NULL, true, '2022-06-30 07:32:29+00', '2022-06-30 07:32:29+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (390, 97, 1, 6, 3, 2, NULL, 'perferendis', 141, false, NULL, NULL, 89877.00, NULL, true, '2022-06-30 07:32:29+00', '2022-06-30 07:32:29+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (391, 97, 1, NULL, NULL, 6, NULL, 'delectus', 23, false, NULL, NULL, 12803.00, NULL, false, '2022-06-30 07:32:29+00', '2022-06-30 07:32:29+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (392, 98, 1, 1, NULL, 7, NULL, 'ipsa', 152, false, NULL, NULL, 38303.00, NULL, true, '2022-06-30 07:32:30+00', '2022-06-30 07:32:30+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (393, 98, 1, 2, NULL, 1, NULL, 'et', 360, false, NULL, NULL, 63585.00, NULL, true, '2022-06-30 07:32:30+00', '2022-06-30 07:32:30+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (394, 98, 1, 3, NULL, 7, NULL, 'doloremque', 933, false, NULL, NULL, 58768.00, NULL, true, '2022-06-30 07:32:30+00', '2022-06-30 07:32:30+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (395, 98, 1, NULL, NULL, 3, NULL, 'molestiae', 968, false, NULL, NULL, 89522.00, NULL, false, '2022-06-30 07:32:30+00', '2022-06-30 07:32:30+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (396, 99, 1, 1, NULL, 5, NULL, 'nobis', 991, false, NULL, NULL, 34285.00, NULL, true, '2022-06-30 07:32:30+00', '2022-06-30 07:32:30+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (397, 99, 1, 2, NULL, 9, NULL, 'delectus', 940, false, NULL, NULL, 51664.00, NULL, true, '2022-06-30 07:32:30+00', '2022-06-30 07:32:30+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (398, 99, 1, 3, NULL, 8, NULL, 'ad', 642, false, NULL, NULL, 53735.00, NULL, true, '2022-06-30 07:32:31+00', '2022-06-30 07:32:31+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (399, 99, 1, NULL, NULL, 5, NULL, 'qui', 672, false, NULL, NULL, 27202.00, NULL, false, '2022-06-30 07:32:31+00', '2022-06-30 07:32:31+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (400, 100, 1, 1, NULL, 4, NULL, 'fuga', 288, false, NULL, NULL, 94605.00, NULL, true, '2022-06-30 07:32:31+00', '2022-06-30 07:32:31+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (401, 100, 1, 2, NULL, 5, NULL, 'explicabo', 557, false, NULL, NULL, 88602.00, NULL, true, '2022-06-30 07:32:31+00', '2022-06-30 07:32:31+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (402, 100, 1, 3, NULL, 9, NULL, 'quia', 575, false, NULL, NULL, 60324.00, NULL, true, '2022-06-30 07:32:31+00', '2022-06-30 07:32:31+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (403, 100, 1, NULL, NULL, 9, NULL, 'delectus', 303, false, NULL, NULL, 85589.00, NULL, false, '2022-06-30 07:32:31+00', '2022-06-30 07:32:31+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (404, 101, 1, 4, NULL, 4, NULL, 'provident', 636, false, NULL, NULL, 43827.00, NULL, true, '2022-06-30 07:32:32+00', '2022-06-30 07:32:32+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (405, 101, 1, 5, NULL, 3, NULL, 'vero', 780, false, NULL, NULL, 65054.00, NULL, true, '2022-06-30 07:32:32+00', '2022-06-30 07:32:32+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (406, 101, 1, 6, NULL, 4, NULL, 'quia', 420, false, NULL, NULL, 7081.00, NULL, true, '2022-06-30 07:32:32+00', '2022-06-30 07:32:32+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (407, 101, 1, NULL, NULL, 1, NULL, 'aut', 206, false, NULL, NULL, 73433.00, NULL, false, '2022-06-30 07:32:32+00', '2022-06-30 07:32:32+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (408, 102, 1, 4, NULL, 6, NULL, 'ea', 928, false, NULL, NULL, 22502.00, NULL, true, '2022-06-30 07:32:32+00', '2022-06-30 07:32:32+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (409, 102, 1, 5, NULL, 2, NULL, 'aliquam', 925, false, NULL, NULL, 81629.00, NULL, true, '2022-06-30 07:32:32+00', '2022-06-30 07:32:32+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (410, 102, 1, 6, NULL, 8, NULL, 'et', 608, false, NULL, NULL, 22338.00, NULL, true, '2022-06-30 07:32:32+00', '2022-06-30 07:32:32+00', 'JPY', NULL, 'productclass');
INSERT INTO public.dtb_product_class VALUES (411, 102, 1, NULL, NULL, 2, NULL, 'neque', 441, false, NULL, NULL, 45220.00, NULL, false, '2022-06-30 07:32:32+00', '2022-06-30 07:32:32+00', 'JPY', NULL, 'productclass');


--
-- Data for Name: dtb_product_image; Type: TABLE DATA; Schema: public; Owner: dbuser
--

INSERT INTO public.dtb_product_image VALUES (1, 1, NULL, 'cube-1.png', 1, '2017-03-07 10:14:52+00', 'productimage');
INSERT INTO public.dtb_product_image VALUES (2, 1, NULL, 'cube-2.png', 2, '2017-03-07 10:14:52+00', 'productimage');
INSERT INTO public.dtb_product_image VALUES (3, 1, NULL, 'cube-3.png', 3, '2017-03-07 10:14:52+00', 'productimage');
INSERT INTO public.dtb_product_image VALUES (4, 2, NULL, 'sand-1.png', 1, '2017-03-07 10:14:52+00', 'productimage');
INSERT INTO public.dtb_product_image VALUES (5, 2, NULL, 'sand-2.png', 2, '2017-03-07 10:14:52+00', 'productimage');
INSERT INTO public.dtb_product_image VALUES (6, 2, NULL, 'sand-3.png', 3, '2017-03-07 10:14:52+00', 'productimage');
INSERT INTO public.dtb_product_image VALUES (7, 3, NULL, 'aut.jpg', 0, '2022-06-30 07:31:54+00', 'productimage');
INSERT INTO public.dtb_product_image VALUES (8, 3, NULL, 'et.jpg', 1, '2022-06-30 07:31:54+00', 'productimage');
INSERT INTO public.dtb_product_image VALUES (9, 3, NULL, 'nam.jpg', 2, '2022-06-30 07:31:54+00', 'productimage');
INSERT INTO public.dtb_product_image VALUES (10, 4, NULL, 'vel.jpg', 0, '2022-06-30 07:31:55+00', 'productimage');
INSERT INTO public.dtb_product_image VALUES (11, 4, NULL, 'accusantium.jpg', 1, '2022-06-30 07:31:55+00', 'productimage');
INSERT INTO public.dtb_product_image VALUES (12, 4, NULL, 'non.jpg', 2, '2022-06-30 07:31:55+00', 'productimage');
INSERT INTO public.dtb_product_image VALUES (13, 5, NULL, 'consequuntur.jpg', 0, '2022-06-30 07:31:55+00', 'productimage');
INSERT INTO public.dtb_product_image VALUES (14, 5, NULL, 'quis.jpg', 1, '2022-06-30 07:31:55+00', 'productimage');
INSERT INTO public.dtb_product_image VALUES (15, 5, NULL, 'fugit.jpg', 2, '2022-06-30 07:31:55+00', 'productimage');
INSERT INTO public.dtb_product_image VALUES (16, 6, NULL, 'ullam.jpg', 0, '2022-06-30 07:31:55+00', 'productimage');
INSERT INTO public.dtb_product_image VALUES (17, 6, NULL, 'explicabo.jpg', 1, '2022-06-30 07:31:55+00', 'productimage');
INSERT INTO public.dtb_product_image VALUES (18, 6, NULL, 'sit.jpg', 2, '2022-06-30 07:31:55+00', 'productimage');
INSERT INTO public.dtb_product_image VALUES (19, 7, NULL, 'aperiam.jpg', 0, '2022-06-30 07:31:55+00', 'productimage');
INSERT INTO public.dtb_product_image VALUES (20, 7, NULL, 'adipisci.jpg', 1, '2022-06-30 07:31:55+00', 'productimage');
INSERT INTO public.dtb_product_image VALUES (21, 7, NULL, 'architecto.jpg', 2, '2022-06-30 07:31:55+00', 'productimage');
INSERT INTO public.dtb_product_image VALUES (22, 8, NULL, 'eum.jpg', 0, '2022-06-30 07:31:55+00', 'productimage');
INSERT INTO public.dtb_product_image VALUES (23, 8, NULL, 'consequatur.jpg', 1, '2022-06-30 07:31:55+00', 'productimage');
INSERT INTO public.dtb_product_image VALUES (24, 8, NULL, 'nulla.jpg', 2, '2022-06-30 07:31:55+00', 'productimage');
INSERT INTO public.dtb_product_image VALUES (25, 9, NULL, 'blanditiis.jpg', 0, '2022-06-30 07:31:55+00', 'productimage');
INSERT INTO public.dtb_product_image VALUES (26, 9, NULL, 'nemo.jpg', 1, '2022-06-30 07:31:55+00', 'productimage');
INSERT INTO public.dtb_product_image VALUES (27, 9, NULL, 'et.jpg', 2, '2022-06-30 07:31:55+00', 'productimage');
INSERT INTO public.dtb_product_image VALUES (28, 10, NULL, 'voluptates.jpg', 0, '2022-06-30 07:31:55+00', 'productimage');
INSERT INTO public.dtb_product_image VALUES (29, 10, NULL, 'iusto.jpg', 1, '2022-06-30 07:31:55+00', 'productimage');
INSERT INTO public.dtb_product_image VALUES (30, 10, NULL, 'sed.jpg', 2, '2022-06-30 07:31:55+00', 'productimage');
INSERT INTO public.dtb_product_image VALUES (31, 11, NULL, 'delectus.jpg', 0, '2022-06-30 07:31:56+00', 'productimage');
INSERT INTO public.dtb_product_image VALUES (32, 11, NULL, 'harum.jpg', 1, '2022-06-30 07:31:56+00', 'productimage');
INSERT INTO public.dtb_product_image VALUES (33, 11, NULL, 'ea.jpg', 2, '2022-06-30 07:31:56+00', 'productimage');
INSERT INTO public.dtb_product_image VALUES (34, 12, NULL, 'sed.jpg', 0, '2022-06-30 07:31:56+00', 'productimage');
INSERT INTO public.dtb_product_image VALUES (35, 12, NULL, 'id.jpg', 1, '2022-06-30 07:31:56+00', 'productimage');
INSERT INTO public.dtb_product_image VALUES (36, 12, NULL, 'nesciunt.jpg', 2, '2022-06-30 07:31:56+00', 'productimage');
INSERT INTO public.dtb_product_image VALUES (37, 13, NULL, 'quod.jpg', 0, '2022-06-30 07:31:56+00', 'productimage');
INSERT INTO public.dtb_product_image VALUES (38, 13, NULL, 'nisi.jpg', 1, '2022-06-30 07:31:56+00', 'productimage');
INSERT INTO public.dtb_product_image VALUES (39, 13, NULL, 'ut.jpg', 2, '2022-06-30 07:31:56+00', 'productimage');
INSERT INTO public.dtb_product_image VALUES (40, 14, NULL, 'consequatur.jpg', 0, '2022-06-30 07:31:56+00', 'productimage');
INSERT INTO public.dtb_product_image VALUES (41, 14, NULL, 'et.jpg', 1, '2022-06-30 07:31:56+00', 'productimage');
INSERT INTO public.dtb_product_image VALUES (42, 14, NULL, 'cumque.jpg', 2, '2022-06-30 07:31:56+00', 'productimage');
INSERT INTO public.dtb_product_image VALUES (43, 15, NULL, 'adipisci.jpg', 0, '2022-06-30 07:31:56+00', 'productimage');
INSERT INTO public.dtb_product_image VALUES (44, 15, NULL, 'ea.jpg', 1, '2022-06-30 07:31:56+00', 'productimage');
INSERT INTO public.dtb_product_image VALUES (45, 15, NULL, 'quis.jpg', 2, '2022-06-30 07:31:56+00', 'productimage');
INSERT INTO public.dtb_product_image VALUES (46, 16, NULL, 'occaecati.jpg', 0, '2022-06-30 07:31:56+00', 'productimage');
INSERT INTO public.dtb_product_image VALUES (47, 16, NULL, 'molestiae.jpg', 1, '2022-06-30 07:31:56+00', 'productimage');
INSERT INTO public.dtb_product_image VALUES (48, 16, NULL, 'ullam.jpg', 2, '2022-06-30 07:31:56+00', 'productimage');
INSERT INTO public.dtb_product_image VALUES (49, 17, NULL, 'et.jpg', 0, '2022-06-30 07:31:57+00', 'productimage');
INSERT INTO public.dtb_product_image VALUES (50, 17, NULL, 'in.jpg', 1, '2022-06-30 07:31:57+00', 'productimage');
INSERT INTO public.dtb_product_image VALUES (51, 17, NULL, 'necessitatibus.jpg', 2, '2022-06-30 07:31:57+00', 'productimage');
INSERT INTO public.dtb_product_image VALUES (52, 18, NULL, 'ab.jpg', 0, '2022-06-30 07:31:57+00', 'productimage');
INSERT INTO public.dtb_product_image VALUES (53, 18, NULL, 'praesentium.jpg', 1, '2022-06-30 07:31:57+00', 'productimage');
INSERT INTO public.dtb_product_image VALUES (54, 18, NULL, 'praesentium.jpg', 2, '2022-06-30 07:31:57+00', 'productimage');
INSERT INTO public.dtb_product_image VALUES (55, 19, NULL, 'dolore.jpg', 0, '2022-06-30 07:31:57+00', 'productimage');
INSERT INTO public.dtb_product_image VALUES (56, 19, NULL, 'voluptas.jpg', 1, '2022-06-30 07:31:57+00', 'productimage');
INSERT INTO public.dtb_product_image VALUES (57, 19, NULL, 'labore.jpg', 2, '2022-06-30 07:31:57+00', 'productimage');
INSERT INTO public.dtb_product_image VALUES (58, 20, NULL, 'et.jpg', 0, '2022-06-30 07:31:57+00', 'productimage');
INSERT INTO public.dtb_product_image VALUES (59, 20, NULL, 'rerum.jpg', 1, '2022-06-30 07:31:57+00', 'productimage');
INSERT INTO public.dtb_product_image VALUES (60, 20, NULL, 'in.jpg', 2, '2022-06-30 07:31:57+00', 'productimage');
INSERT INTO public.dtb_product_image VALUES (61, 21, NULL, 'aut.jpg', 0, '2022-06-30 07:31:57+00', 'productimage');
INSERT INTO public.dtb_product_image VALUES (62, 21, NULL, 'hic.jpg', 1, '2022-06-30 07:31:57+00', 'productimage');
INSERT INTO public.dtb_product_image VALUES (63, 21, NULL, 'fugiat.jpg', 2, '2022-06-30 07:31:57+00', 'productimage');
INSERT INTO public.dtb_product_image VALUES (64, 22, NULL, 'et.jpg', 0, '2022-06-30 07:31:58+00', 'productimage');
INSERT INTO public.dtb_product_image VALUES (65, 22, NULL, 'qui.jpg', 1, '2022-06-30 07:31:58+00', 'productimage');
INSERT INTO public.dtb_product_image VALUES (66, 22, NULL, 'ut.jpg', 2, '2022-06-30 07:31:58+00', 'productimage');
INSERT INTO public.dtb_product_image VALUES (67, 23, NULL, 'nihil.jpg', 0, '2022-06-30 07:31:58+00', 'productimage');
INSERT INTO public.dtb_product_image VALUES (68, 23, NULL, 'amet.jpg', 1, '2022-06-30 07:31:58+00', 'productimage');
INSERT INTO public.dtb_product_image VALUES (69, 23, NULL, 'nam.jpg', 2, '2022-06-30 07:31:58+00', 'productimage');
INSERT INTO public.dtb_product_image VALUES (70, 24, NULL, 'iste.jpg', 0, '2022-06-30 07:31:58+00', 'productimage');
INSERT INTO public.dtb_product_image VALUES (71, 24, NULL, 'in.jpg', 1, '2022-06-30 07:31:58+00', 'productimage');
INSERT INTO public.dtb_product_image VALUES (72, 24, NULL, 'aut.jpg', 2, '2022-06-30 07:31:58+00', 'productimage');
INSERT INTO public.dtb_product_image VALUES (73, 25, NULL, 'perspiciatis.jpg', 0, '2022-06-30 07:31:58+00', 'productimage');
INSERT INTO public.dtb_product_image VALUES (74, 25, NULL, 'ea.jpg', 1, '2022-06-30 07:31:58+00', 'productimage');
INSERT INTO public.dtb_product_image VALUES (75, 25, NULL, 'ducimus.jpg', 2, '2022-06-30 07:31:58+00', 'productimage');
INSERT INTO public.dtb_product_image VALUES (76, 26, NULL, 'cum.jpg', 0, '2022-06-30 07:31:58+00', 'productimage');
INSERT INTO public.dtb_product_image VALUES (77, 26, NULL, 'ducimus.jpg', 1, '2022-06-30 07:31:58+00', 'productimage');
INSERT INTO public.dtb_product_image VALUES (78, 26, NULL, 'quos.jpg', 2, '2022-06-30 07:31:58+00', 'productimage');
INSERT INTO public.dtb_product_image VALUES (79, 27, NULL, 'eos.jpg', 0, '2022-06-30 07:31:59+00', 'productimage');
INSERT INTO public.dtb_product_image VALUES (80, 27, NULL, 'velit.jpg', 1, '2022-06-30 07:31:59+00', 'productimage');
INSERT INTO public.dtb_product_image VALUES (81, 27, NULL, 'est.jpg', 2, '2022-06-30 07:31:59+00', 'productimage');
INSERT INTO public.dtb_product_image VALUES (82, 28, NULL, 'dignissimos.jpg', 0, '2022-06-30 07:31:59+00', 'productimage');
INSERT INTO public.dtb_product_image VALUES (83, 28, NULL, 'id.jpg', 1, '2022-06-30 07:31:59+00', 'productimage');
INSERT INTO public.dtb_product_image VALUES (84, 28, NULL, 'ea.jpg', 2, '2022-06-30 07:31:59+00', 'productimage');
INSERT INTO public.dtb_product_image VALUES (85, 29, NULL, 'iure.jpg', 0, '2022-06-30 07:31:59+00', 'productimage');
INSERT INTO public.dtb_product_image VALUES (86, 29, NULL, 'necessitatibus.jpg', 1, '2022-06-30 07:31:59+00', 'productimage');
INSERT INTO public.dtb_product_image VALUES (87, 29, NULL, 'expedita.jpg', 2, '2022-06-30 07:31:59+00', 'productimage');
INSERT INTO public.dtb_product_image VALUES (88, 30, NULL, 'optio.jpg', 0, '2022-06-30 07:32:00+00', 'productimage');
INSERT INTO public.dtb_product_image VALUES (89, 30, NULL, 'voluptatem.jpg', 1, '2022-06-30 07:32:00+00', 'productimage');
INSERT INTO public.dtb_product_image VALUES (90, 30, NULL, 'placeat.jpg', 2, '2022-06-30 07:32:00+00', 'productimage');
INSERT INTO public.dtb_product_image VALUES (91, 31, NULL, 'vitae.jpg', 0, '2022-06-30 07:32:00+00', 'productimage');
INSERT INTO public.dtb_product_image VALUES (92, 31, NULL, 'repellendus.jpg', 1, '2022-06-30 07:32:00+00', 'productimage');
INSERT INTO public.dtb_product_image VALUES (93, 31, NULL, 'architecto.jpg', 2, '2022-06-30 07:32:00+00', 'productimage');
INSERT INTO public.dtb_product_image VALUES (94, 32, NULL, 'similique.jpg', 0, '2022-06-30 07:32:00+00', 'productimage');
INSERT INTO public.dtb_product_image VALUES (95, 32, NULL, 'facere.jpg', 1, '2022-06-30 07:32:00+00', 'productimage');
INSERT INTO public.dtb_product_image VALUES (96, 32, NULL, 'ducimus.jpg', 2, '2022-06-30 07:32:00+00', 'productimage');
INSERT INTO public.dtb_product_image VALUES (97, 33, NULL, 'rerum.jpg', 0, '2022-06-30 07:32:00+00', 'productimage');
INSERT INTO public.dtb_product_image VALUES (98, 33, NULL, 'nobis.jpg', 1, '2022-06-30 07:32:00+00', 'productimage');
INSERT INTO public.dtb_product_image VALUES (99, 33, NULL, 'adipisci.jpg', 2, '2022-06-30 07:32:00+00', 'productimage');
INSERT INTO public.dtb_product_image VALUES (100, 34, NULL, 'voluptas.jpg', 0, '2022-06-30 07:32:01+00', 'productimage');
INSERT INTO public.dtb_product_image VALUES (101, 34, NULL, 'consequatur.jpg', 1, '2022-06-30 07:32:01+00', 'productimage');
INSERT INTO public.dtb_product_image VALUES (102, 34, NULL, 'laborum.jpg', 2, '2022-06-30 07:32:01+00', 'productimage');
INSERT INTO public.dtb_product_image VALUES (103, 35, NULL, 'nihil.jpg', 0, '2022-06-30 07:32:01+00', 'productimage');
INSERT INTO public.dtb_product_image VALUES (104, 35, NULL, 'aut.jpg', 1, '2022-06-30 07:32:01+00', 'productimage');
INSERT INTO public.dtb_product_image VALUES (105, 35, NULL, 'inventore.jpg', 2, '2022-06-30 07:32:01+00', 'productimage');
INSERT INTO public.dtb_product_image VALUES (106, 36, NULL, 'ea.jpg', 0, '2022-06-30 07:32:01+00', 'productimage');
INSERT INTO public.dtb_product_image VALUES (107, 36, NULL, 'enim.jpg', 1, '2022-06-30 07:32:01+00', 'productimage');
INSERT INTO public.dtb_product_image VALUES (108, 36, NULL, 'quis.jpg', 2, '2022-06-30 07:32:01+00', 'productimage');
INSERT INTO public.dtb_product_image VALUES (109, 37, NULL, 'ut.jpg', 0, '2022-06-30 07:32:01+00', 'productimage');
INSERT INTO public.dtb_product_image VALUES (110, 37, NULL, 'sapiente.jpg', 1, '2022-06-30 07:32:01+00', 'productimage');
INSERT INTO public.dtb_product_image VALUES (111, 37, NULL, 'suscipit.jpg', 2, '2022-06-30 07:32:01+00', 'productimage');
INSERT INTO public.dtb_product_image VALUES (112, 38, NULL, 'nulla.jpg', 0, '2022-06-30 07:32:02+00', 'productimage');
INSERT INTO public.dtb_product_image VALUES (113, 38, NULL, 'non.jpg', 1, '2022-06-30 07:32:02+00', 'productimage');
INSERT INTO public.dtb_product_image VALUES (114, 38, NULL, 'dolor.jpg', 2, '2022-06-30 07:32:02+00', 'productimage');
INSERT INTO public.dtb_product_image VALUES (115, 39, NULL, 'voluptate.jpg', 0, '2022-06-30 07:32:02+00', 'productimage');
INSERT INTO public.dtb_product_image VALUES (116, 39, NULL, 'accusantium.jpg', 1, '2022-06-30 07:32:02+00', 'productimage');
INSERT INTO public.dtb_product_image VALUES (117, 39, NULL, 'consectetur.jpg', 2, '2022-06-30 07:32:02+00', 'productimage');
INSERT INTO public.dtb_product_image VALUES (118, 40, NULL, 'assumenda.jpg', 0, '2022-06-30 07:32:02+00', 'productimage');
INSERT INTO public.dtb_product_image VALUES (119, 40, NULL, 'reprehenderit.jpg', 1, '2022-06-30 07:32:02+00', 'productimage');
INSERT INTO public.dtb_product_image VALUES (120, 40, NULL, 'eum.jpg', 2, '2022-06-30 07:32:02+00', 'productimage');
INSERT INTO public.dtb_product_image VALUES (121, 41, NULL, 'sit.jpg', 0, '2022-06-30 07:32:03+00', 'productimage');
INSERT INTO public.dtb_product_image VALUES (122, 41, NULL, 'ipsam.jpg', 1, '2022-06-30 07:32:03+00', 'productimage');
INSERT INTO public.dtb_product_image VALUES (123, 41, NULL, 'unde.jpg', 2, '2022-06-30 07:32:03+00', 'productimage');
INSERT INTO public.dtb_product_image VALUES (124, 42, NULL, 'quidem.jpg', 0, '2022-06-30 07:32:03+00', 'productimage');
INSERT INTO public.dtb_product_image VALUES (125, 42, NULL, 'ipsa.jpg', 1, '2022-06-30 07:32:03+00', 'productimage');
INSERT INTO public.dtb_product_image VALUES (126, 42, NULL, 'qui.jpg', 2, '2022-06-30 07:32:03+00', 'productimage');
INSERT INTO public.dtb_product_image VALUES (127, 43, NULL, 'qui.jpg', 0, '2022-06-30 07:32:03+00', 'productimage');
INSERT INTO public.dtb_product_image VALUES (128, 43, NULL, 'unde.jpg', 1, '2022-06-30 07:32:03+00', 'productimage');
INSERT INTO public.dtb_product_image VALUES (129, 43, NULL, 'animi.jpg', 2, '2022-06-30 07:32:03+00', 'productimage');
INSERT INTO public.dtb_product_image VALUES (130, 44, NULL, 'quis.jpg', 0, '2022-06-30 07:32:04+00', 'productimage');
INSERT INTO public.dtb_product_image VALUES (131, 44, NULL, 'enim.jpg', 1, '2022-06-30 07:32:04+00', 'productimage');
INSERT INTO public.dtb_product_image VALUES (132, 44, NULL, 'laborum.jpg', 2, '2022-06-30 07:32:04+00', 'productimage');
INSERT INTO public.dtb_product_image VALUES (133, 45, NULL, 'magni.jpg', 0, '2022-06-30 07:32:04+00', 'productimage');
INSERT INTO public.dtb_product_image VALUES (134, 45, NULL, 'molestias.jpg', 1, '2022-06-30 07:32:04+00', 'productimage');
INSERT INTO public.dtb_product_image VALUES (135, 45, NULL, 'aut.jpg', 2, '2022-06-30 07:32:04+00', 'productimage');
INSERT INTO public.dtb_product_image VALUES (136, 46, NULL, 'quia.jpg', 0, '2022-06-30 07:32:04+00', 'productimage');
INSERT INTO public.dtb_product_image VALUES (137, 46, NULL, 'voluptatibus.jpg', 1, '2022-06-30 07:32:04+00', 'productimage');
INSERT INTO public.dtb_product_image VALUES (138, 46, NULL, 'et.jpg', 2, '2022-06-30 07:32:04+00', 'productimage');
INSERT INTO public.dtb_product_image VALUES (139, 47, NULL, 'voluptatem.jpg', 0, '2022-06-30 07:32:05+00', 'productimage');
INSERT INTO public.dtb_product_image VALUES (140, 47, NULL, 'doloribus.jpg', 1, '2022-06-30 07:32:05+00', 'productimage');
INSERT INTO public.dtb_product_image VALUES (141, 47, NULL, 'magni.jpg', 2, '2022-06-30 07:32:05+00', 'productimage');
INSERT INTO public.dtb_product_image VALUES (142, 48, NULL, 'repellat.jpg', 0, '2022-06-30 07:32:05+00', 'productimage');
INSERT INTO public.dtb_product_image VALUES (143, 48, NULL, 'provident.jpg', 1, '2022-06-30 07:32:05+00', 'productimage');
INSERT INTO public.dtb_product_image VALUES (144, 48, NULL, 'quasi.jpg', 2, '2022-06-30 07:32:05+00', 'productimage');
INSERT INTO public.dtb_product_image VALUES (145, 49, NULL, 'non.jpg', 0, '2022-06-30 07:32:05+00', 'productimage');
INSERT INTO public.dtb_product_image VALUES (146, 49, NULL, 'laudantium.jpg', 1, '2022-06-30 07:32:05+00', 'productimage');
INSERT INTO public.dtb_product_image VALUES (147, 49, NULL, 'quia.jpg', 2, '2022-06-30 07:32:05+00', 'productimage');
INSERT INTO public.dtb_product_image VALUES (148, 50, NULL, 'tempore.jpg', 0, '2022-06-30 07:32:06+00', 'productimage');
INSERT INTO public.dtb_product_image VALUES (149, 50, NULL, 'ipsa.jpg', 1, '2022-06-30 07:32:06+00', 'productimage');
INSERT INTO public.dtb_product_image VALUES (150, 50, NULL, 'distinctio.jpg', 2, '2022-06-30 07:32:06+00', 'productimage');
INSERT INTO public.dtb_product_image VALUES (151, 51, NULL, 'soluta.jpg', 0, '2022-06-30 07:32:06+00', 'productimage');
INSERT INTO public.dtb_product_image VALUES (152, 51, NULL, 'ut.jpg', 1, '2022-06-30 07:32:06+00', 'productimage');
INSERT INTO public.dtb_product_image VALUES (153, 51, NULL, 'accusantium.jpg', 2, '2022-06-30 07:32:06+00', 'productimage');
INSERT INTO public.dtb_product_image VALUES (154, 52, NULL, 'quam.jpg', 0, '2022-06-30 07:32:06+00', 'productimage');
INSERT INTO public.dtb_product_image VALUES (155, 52, NULL, 'facere.jpg', 1, '2022-06-30 07:32:06+00', 'productimage');
INSERT INTO public.dtb_product_image VALUES (156, 52, NULL, 'a.jpg', 2, '2022-06-30 07:32:07+00', 'productimage');
INSERT INTO public.dtb_product_image VALUES (157, 53, NULL, 'odit.jpg', 0, '2022-06-30 07:32:07+00', 'productimage');
INSERT INTO public.dtb_product_image VALUES (158, 53, NULL, 'aut.jpg', 1, '2022-06-30 07:32:07+00', 'productimage');
INSERT INTO public.dtb_product_image VALUES (159, 53, NULL, 'nulla.jpg', 2, '2022-06-30 07:32:07+00', 'productimage');
INSERT INTO public.dtb_product_image VALUES (160, 54, NULL, 'repellat.jpg', 0, '2022-06-30 07:32:07+00', 'productimage');
INSERT INTO public.dtb_product_image VALUES (161, 54, NULL, 'qui.jpg', 1, '2022-06-30 07:32:07+00', 'productimage');
INSERT INTO public.dtb_product_image VALUES (162, 54, NULL, 'dolores.jpg', 2, '2022-06-30 07:32:07+00', 'productimage');
INSERT INTO public.dtb_product_image VALUES (163, 55, NULL, 'dignissimos.jpg', 0, '2022-06-30 07:32:08+00', 'productimage');
INSERT INTO public.dtb_product_image VALUES (164, 55, NULL, 'ratione.jpg', 1, '2022-06-30 07:32:08+00', 'productimage');
INSERT INTO public.dtb_product_image VALUES (165, 55, NULL, 'at.jpg', 2, '2022-06-30 07:32:08+00', 'productimage');
INSERT INTO public.dtb_product_image VALUES (166, 56, NULL, 'et.jpg', 0, '2022-06-30 07:32:08+00', 'productimage');
INSERT INTO public.dtb_product_image VALUES (167, 56, NULL, 'pariatur.jpg', 1, '2022-06-30 07:32:08+00', 'productimage');
INSERT INTO public.dtb_product_image VALUES (168, 56, NULL, 'consequatur.jpg', 2, '2022-06-30 07:32:08+00', 'productimage');
INSERT INTO public.dtb_product_image VALUES (169, 57, NULL, 'doloremque.jpg', 0, '2022-06-30 07:32:08+00', 'productimage');
INSERT INTO public.dtb_product_image VALUES (170, 57, NULL, 'est.jpg', 1, '2022-06-30 07:32:09+00', 'productimage');
INSERT INTO public.dtb_product_image VALUES (171, 57, NULL, 'qui.jpg', 2, '2022-06-30 07:32:09+00', 'productimage');
INSERT INTO public.dtb_product_image VALUES (172, 58, NULL, 'officiis.jpg', 0, '2022-06-30 07:32:09+00', 'productimage');
INSERT INTO public.dtb_product_image VALUES (173, 58, NULL, 'voluptatem.jpg', 1, '2022-06-30 07:32:09+00', 'productimage');
INSERT INTO public.dtb_product_image VALUES (174, 58, NULL, 'debitis.jpg', 2, '2022-06-30 07:32:09+00', 'productimage');
INSERT INTO public.dtb_product_image VALUES (175, 59, NULL, 'enim.jpg', 0, '2022-06-30 07:32:09+00', 'productimage');
INSERT INTO public.dtb_product_image VALUES (176, 59, NULL, 'animi.jpg', 1, '2022-06-30 07:32:09+00', 'productimage');
INSERT INTO public.dtb_product_image VALUES (177, 59, NULL, 'iure.jpg', 2, '2022-06-30 07:32:09+00', 'productimage');
INSERT INTO public.dtb_product_image VALUES (178, 60, NULL, 'minus.jpg', 0, '2022-06-30 07:32:10+00', 'productimage');
INSERT INTO public.dtb_product_image VALUES (179, 60, NULL, 'quibusdam.jpg', 1, '2022-06-30 07:32:10+00', 'productimage');
INSERT INTO public.dtb_product_image VALUES (180, 60, NULL, 'eos.jpg', 2, '2022-06-30 07:32:10+00', 'productimage');
INSERT INTO public.dtb_product_image VALUES (181, 61, NULL, 'perspiciatis.jpg', 0, '2022-06-30 07:32:10+00', 'productimage');
INSERT INTO public.dtb_product_image VALUES (182, 61, NULL, 'aut.jpg', 1, '2022-06-30 07:32:10+00', 'productimage');
INSERT INTO public.dtb_product_image VALUES (183, 61, NULL, 'est.jpg', 2, '2022-06-30 07:32:10+00', 'productimage');
INSERT INTO public.dtb_product_image VALUES (184, 62, NULL, 'ad.jpg', 0, '2022-06-30 07:32:11+00', 'productimage');
INSERT INTO public.dtb_product_image VALUES (185, 62, NULL, 'natus.jpg', 1, '2022-06-30 07:32:11+00', 'productimage');
INSERT INTO public.dtb_product_image VALUES (186, 62, NULL, 'est.jpg', 2, '2022-06-30 07:32:11+00', 'productimage');
INSERT INTO public.dtb_product_image VALUES (187, 63, NULL, 'assumenda.jpg', 0, '2022-06-30 07:32:11+00', 'productimage');
INSERT INTO public.dtb_product_image VALUES (188, 63, NULL, 'asperiores.jpg', 1, '2022-06-30 07:32:11+00', 'productimage');
INSERT INTO public.dtb_product_image VALUES (189, 63, NULL, 'cupiditate.jpg', 2, '2022-06-30 07:32:11+00', 'productimage');
INSERT INTO public.dtb_product_image VALUES (190, 64, NULL, 'accusamus.jpg', 0, '2022-06-30 07:32:11+00', 'productimage');
INSERT INTO public.dtb_product_image VALUES (191, 64, NULL, 'ut.jpg', 1, '2022-06-30 07:32:11+00', 'productimage');
INSERT INTO public.dtb_product_image VALUES (192, 64, NULL, 'non.jpg', 2, '2022-06-30 07:32:11+00', 'productimage');
INSERT INTO public.dtb_product_image VALUES (193, 65, NULL, 'suscipit.jpg', 0, '2022-06-30 07:32:12+00', 'productimage');
INSERT INTO public.dtb_product_image VALUES (194, 65, NULL, 'ducimus.jpg', 1, '2022-06-30 07:32:12+00', 'productimage');
INSERT INTO public.dtb_product_image VALUES (195, 65, NULL, 'voluptas.jpg', 2, '2022-06-30 07:32:12+00', 'productimage');
INSERT INTO public.dtb_product_image VALUES (196, 66, NULL, 'hic.jpg', 0, '2022-06-30 07:32:12+00', 'productimage');
INSERT INTO public.dtb_product_image VALUES (197, 66, NULL, 'quos.jpg', 1, '2022-06-30 07:32:12+00', 'productimage');
INSERT INTO public.dtb_product_image VALUES (198, 66, NULL, 'rerum.jpg', 2, '2022-06-30 07:32:12+00', 'productimage');
INSERT INTO public.dtb_product_image VALUES (199, 67, NULL, 'autem.jpg', 0, '2022-06-30 07:32:13+00', 'productimage');
INSERT INTO public.dtb_product_image VALUES (200, 67, NULL, 'voluptas.jpg', 1, '2022-06-30 07:32:13+00', 'productimage');
INSERT INTO public.dtb_product_image VALUES (201, 67, NULL, 'aut.jpg', 2, '2022-06-30 07:32:13+00', 'productimage');
INSERT INTO public.dtb_product_image VALUES (202, 68, NULL, 'facere.jpg', 0, '2022-06-30 07:32:13+00', 'productimage');
INSERT INTO public.dtb_product_image VALUES (203, 68, NULL, 'omnis.jpg', 1, '2022-06-30 07:32:13+00', 'productimage');
INSERT INTO public.dtb_product_image VALUES (204, 68, NULL, 'consequatur.jpg', 2, '2022-06-30 07:32:13+00', 'productimage');
INSERT INTO public.dtb_product_image VALUES (205, 69, NULL, 'voluptatum.jpg', 0, '2022-06-30 07:32:14+00', 'productimage');
INSERT INTO public.dtb_product_image VALUES (206, 69, NULL, 'suscipit.jpg', 1, '2022-06-30 07:32:14+00', 'productimage');
INSERT INTO public.dtb_product_image VALUES (207, 69, NULL, 'id.jpg', 2, '2022-06-30 07:32:14+00', 'productimage');
INSERT INTO public.dtb_product_image VALUES (208, 70, NULL, 'qui.jpg', 0, '2022-06-30 07:32:14+00', 'productimage');
INSERT INTO public.dtb_product_image VALUES (209, 70, NULL, 'sint.jpg', 1, '2022-06-30 07:32:14+00', 'productimage');
INSERT INTO public.dtb_product_image VALUES (210, 70, NULL, 'doloremque.jpg', 2, '2022-06-30 07:32:14+00', 'productimage');
INSERT INTO public.dtb_product_image VALUES (211, 71, NULL, 'animi.jpg', 0, '2022-06-30 07:32:15+00', 'productimage');
INSERT INTO public.dtb_product_image VALUES (212, 71, NULL, 'at.jpg', 1, '2022-06-30 07:32:15+00', 'productimage');
INSERT INTO public.dtb_product_image VALUES (213, 71, NULL, 'vel.jpg', 2, '2022-06-30 07:32:15+00', 'productimage');
INSERT INTO public.dtb_product_image VALUES (214, 72, NULL, 'quia.jpg', 0, '2022-06-30 07:32:15+00', 'productimage');
INSERT INTO public.dtb_product_image VALUES (215, 72, NULL, 'id.jpg', 1, '2022-06-30 07:32:15+00', 'productimage');
INSERT INTO public.dtb_product_image VALUES (216, 72, NULL, 'eveniet.jpg', 2, '2022-06-30 07:32:15+00', 'productimage');
INSERT INTO public.dtb_product_image VALUES (217, 73, NULL, 'assumenda.jpg', 0, '2022-06-30 07:32:16+00', 'productimage');
INSERT INTO public.dtb_product_image VALUES (218, 73, NULL, 'earum.jpg', 1, '2022-06-30 07:32:16+00', 'productimage');
INSERT INTO public.dtb_product_image VALUES (219, 73, NULL, 'reiciendis.jpg', 2, '2022-06-30 07:32:16+00', 'productimage');
INSERT INTO public.dtb_product_image VALUES (220, 74, NULL, 'et.jpg', 0, '2022-06-30 07:32:16+00', 'productimage');
INSERT INTO public.dtb_product_image VALUES (221, 74, NULL, 'earum.jpg', 1, '2022-06-30 07:32:16+00', 'productimage');
INSERT INTO public.dtb_product_image VALUES (222, 74, NULL, 'praesentium.jpg', 2, '2022-06-30 07:32:16+00', 'productimage');
INSERT INTO public.dtb_product_image VALUES (223, 75, NULL, 'tenetur.jpg', 0, '2022-06-30 07:32:17+00', 'productimage');
INSERT INTO public.dtb_product_image VALUES (224, 75, NULL, 'non.jpg', 1, '2022-06-30 07:32:17+00', 'productimage');
INSERT INTO public.dtb_product_image VALUES (225, 75, NULL, 'qui.jpg', 2, '2022-06-30 07:32:17+00', 'productimage');
INSERT INTO public.dtb_product_image VALUES (226, 76, NULL, 'quas.jpg', 0, '2022-06-30 07:32:17+00', 'productimage');
INSERT INTO public.dtb_product_image VALUES (227, 76, NULL, 'nihil.jpg', 1, '2022-06-30 07:32:17+00', 'productimage');
INSERT INTO public.dtb_product_image VALUES (228, 76, NULL, 'magni.jpg', 2, '2022-06-30 07:32:17+00', 'productimage');
INSERT INTO public.dtb_product_image VALUES (229, 77, NULL, 'voluptas.jpg', 0, '2022-06-30 07:32:18+00', 'productimage');
INSERT INTO public.dtb_product_image VALUES (230, 77, NULL, 'quam.jpg', 1, '2022-06-30 07:32:18+00', 'productimage');
INSERT INTO public.dtb_product_image VALUES (231, 77, NULL, 'suscipit.jpg', 2, '2022-06-30 07:32:18+00', 'productimage');
INSERT INTO public.dtb_product_image VALUES (232, 78, NULL, 'nobis.jpg', 0, '2022-06-30 07:32:18+00', 'productimage');
INSERT INTO public.dtb_product_image VALUES (233, 78, NULL, 'ut.jpg', 1, '2022-06-30 07:32:18+00', 'productimage');
INSERT INTO public.dtb_product_image VALUES (234, 78, NULL, 'quae.jpg', 2, '2022-06-30 07:32:18+00', 'productimage');
INSERT INTO public.dtb_product_image VALUES (235, 79, NULL, 'adipisci.jpg', 0, '2022-06-30 07:32:19+00', 'productimage');
INSERT INTO public.dtb_product_image VALUES (236, 79, NULL, 'id.jpg', 1, '2022-06-30 07:32:19+00', 'productimage');
INSERT INTO public.dtb_product_image VALUES (237, 79, NULL, 'vitae.jpg', 2, '2022-06-30 07:32:19+00', 'productimage');
INSERT INTO public.dtb_product_image VALUES (238, 80, NULL, 'omnis.jpg', 0, '2022-06-30 07:32:19+00', 'productimage');
INSERT INTO public.dtb_product_image VALUES (239, 80, NULL, 'eum.jpg', 1, '2022-06-30 07:32:19+00', 'productimage');
INSERT INTO public.dtb_product_image VALUES (240, 80, NULL, 'asperiores.jpg', 2, '2022-06-30 07:32:19+00', 'productimage');
INSERT INTO public.dtb_product_image VALUES (241, 81, NULL, 'deserunt.jpg', 0, '2022-06-30 07:32:20+00', 'productimage');
INSERT INTO public.dtb_product_image VALUES (242, 81, NULL, 'quia.jpg', 1, '2022-06-30 07:32:20+00', 'productimage');
INSERT INTO public.dtb_product_image VALUES (243, 81, NULL, 'est.jpg', 2, '2022-06-30 07:32:20+00', 'productimage');
INSERT INTO public.dtb_product_image VALUES (244, 82, NULL, 'qui.jpg', 0, '2022-06-30 07:32:20+00', 'productimage');
INSERT INTO public.dtb_product_image VALUES (245, 82, NULL, 'tempore.jpg', 1, '2022-06-30 07:32:20+00', 'productimage');
INSERT INTO public.dtb_product_image VALUES (246, 82, NULL, 'molestias.jpg', 2, '2022-06-30 07:32:20+00', 'productimage');
INSERT INTO public.dtb_product_image VALUES (247, 83, NULL, 'omnis.jpg', 0, '2022-06-30 07:32:21+00', 'productimage');
INSERT INTO public.dtb_product_image VALUES (248, 83, NULL, 'mollitia.jpg', 1, '2022-06-30 07:32:21+00', 'productimage');
INSERT INTO public.dtb_product_image VALUES (249, 83, NULL, 'ducimus.jpg', 2, '2022-06-30 07:32:21+00', 'productimage');
INSERT INTO public.dtb_product_image VALUES (250, 84, NULL, 'et.jpg', 0, '2022-06-30 07:32:21+00', 'productimage');
INSERT INTO public.dtb_product_image VALUES (251, 84, NULL, 'labore.jpg', 1, '2022-06-30 07:32:21+00', 'productimage');
INSERT INTO public.dtb_product_image VALUES (252, 84, NULL, 'recusandae.jpg', 2, '2022-06-30 07:32:21+00', 'productimage');
INSERT INTO public.dtb_product_image VALUES (253, 85, NULL, 'tenetur.jpg', 0, '2022-06-30 07:32:22+00', 'productimage');
INSERT INTO public.dtb_product_image VALUES (254, 85, NULL, 'quas.jpg', 1, '2022-06-30 07:32:22+00', 'productimage');
INSERT INTO public.dtb_product_image VALUES (255, 85, NULL, 'debitis.jpg', 2, '2022-06-30 07:32:22+00', 'productimage');
INSERT INTO public.dtb_product_image VALUES (256, 86, NULL, 'facilis.jpg', 0, '2022-06-30 07:32:22+00', 'productimage');
INSERT INTO public.dtb_product_image VALUES (257, 86, NULL, 'non.jpg', 1, '2022-06-30 07:32:22+00', 'productimage');
INSERT INTO public.dtb_product_image VALUES (258, 86, NULL, 'sit.jpg', 2, '2022-06-30 07:32:23+00', 'productimage');
INSERT INTO public.dtb_product_image VALUES (259, 87, NULL, 'quas.jpg', 0, '2022-06-30 07:32:23+00', 'productimage');
INSERT INTO public.dtb_product_image VALUES (260, 87, NULL, 'commodi.jpg', 1, '2022-06-30 07:32:23+00', 'productimage');
INSERT INTO public.dtb_product_image VALUES (261, 87, NULL, 'sequi.jpg', 2, '2022-06-30 07:32:23+00', 'productimage');
INSERT INTO public.dtb_product_image VALUES (262, 88, NULL, 'maxime.jpg', 0, '2022-06-30 07:32:24+00', 'productimage');
INSERT INTO public.dtb_product_image VALUES (263, 88, NULL, 'eligendi.jpg', 1, '2022-06-30 07:32:24+00', 'productimage');
INSERT INTO public.dtb_product_image VALUES (264, 88, NULL, 'ut.jpg', 2, '2022-06-30 07:32:24+00', 'productimage');
INSERT INTO public.dtb_product_image VALUES (265, 89, NULL, 'sit.jpg', 0, '2022-06-30 07:32:24+00', 'productimage');
INSERT INTO public.dtb_product_image VALUES (266, 89, NULL, 'nobis.jpg', 1, '2022-06-30 07:32:24+00', 'productimage');
INSERT INTO public.dtb_product_image VALUES (267, 89, NULL, 'nam.jpg', 2, '2022-06-30 07:32:24+00', 'productimage');
INSERT INTO public.dtb_product_image VALUES (268, 90, NULL, 'perspiciatis.jpg', 0, '2022-06-30 07:32:25+00', 'productimage');
INSERT INTO public.dtb_product_image VALUES (269, 90, NULL, 'veritatis.jpg', 1, '2022-06-30 07:32:25+00', 'productimage');
INSERT INTO public.dtb_product_image VALUES (270, 90, NULL, 'voluptatem.jpg', 2, '2022-06-30 07:32:25+00', 'productimage');
INSERT INTO public.dtb_product_image VALUES (271, 91, NULL, 'ab.jpg', 0, '2022-06-30 07:32:25+00', 'productimage');
INSERT INTO public.dtb_product_image VALUES (272, 91, NULL, 'asperiores.jpg', 1, '2022-06-30 07:32:25+00', 'productimage');
INSERT INTO public.dtb_product_image VALUES (273, 91, NULL, 'iure.jpg', 2, '2022-06-30 07:32:25+00', 'productimage');
INSERT INTO public.dtb_product_image VALUES (274, 92, NULL, 'ab.jpg', 0, '2022-06-30 07:32:26+00', 'productimage');
INSERT INTO public.dtb_product_image VALUES (275, 92, NULL, 'esse.jpg', 1, '2022-06-30 07:32:26+00', 'productimage');
INSERT INTO public.dtb_product_image VALUES (276, 92, NULL, 'voluptas.jpg', 2, '2022-06-30 07:32:26+00', 'productimage');
INSERT INTO public.dtb_product_image VALUES (277, 93, NULL, 'sequi.jpg', 0, '2022-06-30 07:32:27+00', 'productimage');
INSERT INTO public.dtb_product_image VALUES (278, 93, NULL, 'id.jpg', 1, '2022-06-30 07:32:27+00', 'productimage');
INSERT INTO public.dtb_product_image VALUES (279, 93, NULL, 'eos.jpg', 2, '2022-06-30 07:32:27+00', 'productimage');
INSERT INTO public.dtb_product_image VALUES (280, 94, NULL, 'temporibus.jpg', 0, '2022-06-30 07:32:27+00', 'productimage');
INSERT INTO public.dtb_product_image VALUES (281, 94, NULL, 'quae.jpg', 1, '2022-06-30 07:32:27+00', 'productimage');
INSERT INTO public.dtb_product_image VALUES (282, 94, NULL, 'magnam.jpg', 2, '2022-06-30 07:32:27+00', 'productimage');
INSERT INTO public.dtb_product_image VALUES (283, 95, NULL, 'neque.jpg', 0, '2022-06-30 07:32:28+00', 'productimage');
INSERT INTO public.dtb_product_image VALUES (284, 95, NULL, 'qui.jpg', 1, '2022-06-30 07:32:28+00', 'productimage');
INSERT INTO public.dtb_product_image VALUES (285, 95, NULL, 'iure.jpg', 2, '2022-06-30 07:32:28+00', 'productimage');
INSERT INTO public.dtb_product_image VALUES (286, 96, NULL, 'id.jpg', 0, '2022-06-30 07:32:28+00', 'productimage');
INSERT INTO public.dtb_product_image VALUES (287, 96, NULL, 'itaque.jpg', 1, '2022-06-30 07:32:28+00', 'productimage');
INSERT INTO public.dtb_product_image VALUES (288, 96, NULL, 'esse.jpg', 2, '2022-06-30 07:32:28+00', 'productimage');
INSERT INTO public.dtb_product_image VALUES (289, 97, NULL, 'voluptates.jpg', 0, '2022-06-30 07:32:29+00', 'productimage');
INSERT INTO public.dtb_product_image VALUES (290, 97, NULL, 'velit.jpg', 1, '2022-06-30 07:32:29+00', 'productimage');
INSERT INTO public.dtb_product_image VALUES (291, 97, NULL, 'non.jpg', 2, '2022-06-30 07:32:29+00', 'productimage');
INSERT INTO public.dtb_product_image VALUES (292, 98, NULL, 'eum.jpg', 0, '2022-06-30 07:32:30+00', 'productimage');
INSERT INTO public.dtb_product_image VALUES (293, 98, NULL, 'numquam.jpg', 1, '2022-06-30 07:32:30+00', 'productimage');
INSERT INTO public.dtb_product_image VALUES (294, 98, NULL, 'ut.jpg', 2, '2022-06-30 07:32:30+00', 'productimage');
INSERT INTO public.dtb_product_image VALUES (295, 99, NULL, 'eos.jpg', 0, '2022-06-30 07:32:30+00', 'productimage');
INSERT INTO public.dtb_product_image VALUES (296, 99, NULL, 'rerum.jpg', 1, '2022-06-30 07:32:30+00', 'productimage');
INSERT INTO public.dtb_product_image VALUES (297, 99, NULL, 'repudiandae.jpg', 2, '2022-06-30 07:32:30+00', 'productimage');
INSERT INTO public.dtb_product_image VALUES (298, 100, NULL, 'incidunt.jpg', 0, '2022-06-30 07:32:31+00', 'productimage');
INSERT INTO public.dtb_product_image VALUES (299, 100, NULL, 'et.jpg', 1, '2022-06-30 07:32:31+00', 'productimage');
INSERT INTO public.dtb_product_image VALUES (300, 100, NULL, 'illo.jpg', 2, '2022-06-30 07:32:31+00', 'productimage');
INSERT INTO public.dtb_product_image VALUES (301, 101, NULL, 'impedit.jpg', 0, '2022-06-30 07:32:32+00', 'productimage');
INSERT INTO public.dtb_product_image VALUES (302, 101, NULL, 'rerum.jpg', 1, '2022-06-30 07:32:32+00', 'productimage');
INSERT INTO public.dtb_product_image VALUES (303, 101, NULL, 'quia.jpg', 2, '2022-06-30 07:32:32+00', 'productimage');
INSERT INTO public.dtb_product_image VALUES (304, 102, NULL, 'laboriosam.jpg', 0, '2022-06-30 07:32:32+00', 'productimage');
INSERT INTO public.dtb_product_image VALUES (305, 102, NULL, 'dolorem.jpg', 1, '2022-06-30 07:32:32+00', 'productimage');
INSERT INTO public.dtb_product_image VALUES (306, 102, NULL, 'excepturi.jpg', 2, '2022-06-30 07:32:32+00', 'productimage');


--
-- Data for Name: dtb_product_stock; Type: TABLE DATA; Schema: public; Owner: dbuser
--

INSERT INTO public.dtb_product_stock VALUES (1, 1, NULL, NULL, '2017-03-07 10:14:52+00', '2017-03-07 10:14:52+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (2, 2, NULL, NULL, '2017-03-07 10:14:52+00', '2017-03-07 10:14:52+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (3, 3, NULL, NULL, '2017-03-07 10:14:52+00', '2017-03-07 10:14:52+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (4, 4, NULL, NULL, '2017-03-07 10:14:52+00', '2017-03-07 10:14:52+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (5, 5, NULL, NULL, '2017-03-07 10:14:52+00', '2017-03-07 10:14:52+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (6, 6, NULL, NULL, '2017-03-07 10:14:52+00', '2017-03-07 10:14:52+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (7, 7, NULL, NULL, '2017-03-07 10:14:52+00', '2017-03-07 10:14:52+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (8, 8, NULL, NULL, '2017-03-07 10:14:52+00', '2017-03-07 10:14:52+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (9, 9, NULL, NULL, '2017-03-07 10:14:52+00', '2017-03-07 10:14:52+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (10, 10, NULL, NULL, '2017-03-07 10:14:52+00', '2017-03-07 10:14:52+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (11, 11, NULL, 100, '2017-03-07 10:14:52+00', '2017-03-07 10:14:52+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (12, 12, NULL, 489, '2022-06-30 07:31:54+00', '2022-06-30 07:31:54+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (13, 13, NULL, 319, '2022-06-30 07:31:54+00', '2022-06-30 07:31:54+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (14, 14, NULL, 205, '2022-06-30 07:31:54+00', '2022-06-30 07:31:54+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (15, 15, NULL, 330, '2022-06-30 07:31:54+00', '2022-06-30 07:31:54+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (16, 16, NULL, 848, '2022-06-30 07:31:55+00', '2022-06-30 07:31:55+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (17, 17, NULL, 990, '2022-06-30 07:31:55+00', '2022-06-30 07:31:55+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (18, 18, NULL, 478, '2022-06-30 07:31:55+00', '2022-06-30 07:31:55+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (19, 19, NULL, 135, '2022-06-30 07:31:55+00', '2022-06-30 07:31:55+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (20, 20, NULL, 106, '2022-06-30 07:31:55+00', '2022-06-30 07:31:55+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (21, 21, NULL, 680, '2022-06-30 07:31:55+00', '2022-06-30 07:31:55+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (22, 22, NULL, 703, '2022-06-30 07:31:55+00', '2022-06-30 07:31:55+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (23, 23, NULL, 478, '2022-06-30 07:31:55+00', '2022-06-30 07:31:55+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (24, 24, NULL, 317, '2022-06-30 07:31:55+00', '2022-06-30 07:31:55+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (25, 25, NULL, 668, '2022-06-30 07:31:55+00', '2022-06-30 07:31:55+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (26, 26, NULL, 753, '2022-06-30 07:31:55+00', '2022-06-30 07:31:55+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (27, 27, NULL, 324, '2022-06-30 07:31:55+00', '2022-06-30 07:31:55+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (28, 28, NULL, 687, '2022-06-30 07:31:55+00', '2022-06-30 07:31:55+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (29, 29, NULL, 480, '2022-06-30 07:31:55+00', '2022-06-30 07:31:55+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (30, 30, NULL, 276, '2022-06-30 07:31:55+00', '2022-06-30 07:31:55+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (31, 31, NULL, 137, '2022-06-30 07:31:55+00', '2022-06-30 07:31:55+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (32, 32, NULL, 424, '2022-06-30 07:31:55+00', '2022-06-30 07:31:55+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (33, 33, NULL, 459, '2022-06-30 07:31:55+00', '2022-06-30 07:31:55+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (34, 34, NULL, 336, '2022-06-30 07:31:55+00', '2022-06-30 07:31:55+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (35, 35, NULL, 405, '2022-06-30 07:31:55+00', '2022-06-30 07:31:55+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (36, 36, NULL, 296, '2022-06-30 07:31:55+00', '2022-06-30 07:31:55+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (37, 37, NULL, 198, '2022-06-30 07:31:55+00', '2022-06-30 07:31:55+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (38, 38, NULL, 139, '2022-06-30 07:31:55+00', '2022-06-30 07:31:55+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (39, 39, NULL, 249, '2022-06-30 07:31:55+00', '2022-06-30 07:31:55+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (40, 40, NULL, 339, '2022-06-30 07:31:55+00', '2022-06-30 07:31:55+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (41, 41, NULL, 378, '2022-06-30 07:31:55+00', '2022-06-30 07:31:55+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (42, 42, NULL, 582, '2022-06-30 07:31:55+00', '2022-06-30 07:31:55+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (43, 43, NULL, 34, '2022-06-30 07:31:55+00', '2022-06-30 07:31:55+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (44, 44, NULL, 598, '2022-06-30 07:31:56+00', '2022-06-30 07:31:56+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (45, 45, NULL, 816, '2022-06-30 07:31:56+00', '2022-06-30 07:31:56+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (46, 46, NULL, 315, '2022-06-30 07:31:56+00', '2022-06-30 07:31:56+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (47, 47, NULL, 790, '2022-06-30 07:31:56+00', '2022-06-30 07:31:56+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (48, 48, NULL, 422, '2022-06-30 07:31:56+00', '2022-06-30 07:31:56+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (49, 49, NULL, 951, '2022-06-30 07:31:56+00', '2022-06-30 07:31:56+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (50, 50, NULL, 829, '2022-06-30 07:31:56+00', '2022-06-30 07:31:56+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (51, 51, NULL, 349, '2022-06-30 07:31:56+00', '2022-06-30 07:31:56+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (52, 52, NULL, 904, '2022-06-30 07:31:56+00', '2022-06-30 07:31:56+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (53, 53, NULL, 729, '2022-06-30 07:31:56+00', '2022-06-30 07:31:56+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (54, 54, NULL, 519, '2022-06-30 07:31:56+00', '2022-06-30 07:31:56+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (55, 55, NULL, 617, '2022-06-30 07:31:56+00', '2022-06-30 07:31:56+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (56, 56, NULL, 749, '2022-06-30 07:31:56+00', '2022-06-30 07:31:56+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (57, 57, NULL, 787, '2022-06-30 07:31:56+00', '2022-06-30 07:31:56+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (58, 58, NULL, 583, '2022-06-30 07:31:56+00', '2022-06-30 07:31:56+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (59, 59, NULL, 563, '2022-06-30 07:31:56+00', '2022-06-30 07:31:56+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (60, 60, NULL, 994, '2022-06-30 07:31:56+00', '2022-06-30 07:31:56+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (61, 61, NULL, 684, '2022-06-30 07:31:56+00', '2022-06-30 07:31:56+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (62, 62, NULL, 498, '2022-06-30 07:31:56+00', '2022-06-30 07:31:56+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (63, 63, NULL, 411, '2022-06-30 07:31:56+00', '2022-06-30 07:31:56+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (64, 64, NULL, 844, '2022-06-30 07:31:56+00', '2022-06-30 07:31:56+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (65, 65, NULL, 376, '2022-06-30 07:31:56+00', '2022-06-30 07:31:56+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (66, 66, NULL, 701, '2022-06-30 07:31:56+00', '2022-06-30 07:31:56+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (67, 67, NULL, 321, '2022-06-30 07:31:56+00', '2022-06-30 07:31:56+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (68, 68, NULL, 930, '2022-06-30 07:31:57+00', '2022-06-30 07:31:57+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (69, 69, NULL, 413, '2022-06-30 07:31:57+00', '2022-06-30 07:31:57+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (70, 70, NULL, 919, '2022-06-30 07:31:57+00', '2022-06-30 07:31:57+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (71, 71, NULL, 872, '2022-06-30 07:31:57+00', '2022-06-30 07:31:57+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (72, 72, NULL, 779, '2022-06-30 07:31:57+00', '2022-06-30 07:31:57+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (73, 73, NULL, 877, '2022-06-30 07:31:57+00', '2022-06-30 07:31:57+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (74, 74, NULL, 275, '2022-06-30 07:31:57+00', '2022-06-30 07:31:57+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (75, 75, NULL, 667, '2022-06-30 07:31:57+00', '2022-06-30 07:31:57+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (76, 76, NULL, 759, '2022-06-30 07:31:57+00', '2022-06-30 07:31:57+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (77, 77, NULL, 682, '2022-06-30 07:31:57+00', '2022-06-30 07:31:57+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (78, 78, NULL, 250, '2022-06-30 07:31:57+00', '2022-06-30 07:31:57+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (79, 79, NULL, 526, '2022-06-30 07:31:57+00', '2022-06-30 07:31:57+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (80, 80, NULL, 421, '2022-06-30 07:31:57+00', '2022-06-30 07:31:57+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (81, 81, NULL, 881, '2022-06-30 07:31:57+00', '2022-06-30 07:31:57+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (82, 82, NULL, 824, '2022-06-30 07:31:57+00', '2022-06-30 07:31:57+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (83, 83, NULL, 766, '2022-06-30 07:31:57+00', '2022-06-30 07:31:57+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (84, 84, NULL, 249, '2022-06-30 07:31:57+00', '2022-06-30 07:31:57+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (85, 85, NULL, 784, '2022-06-30 07:31:57+00', '2022-06-30 07:31:57+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (86, 86, NULL, 687, '2022-06-30 07:31:57+00', '2022-06-30 07:31:57+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (87, 87, NULL, 697, '2022-06-30 07:31:57+00', '2022-06-30 07:31:57+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (88, 88, NULL, 213, '2022-06-30 07:31:58+00', '2022-06-30 07:31:58+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (89, 89, NULL, 998, '2022-06-30 07:31:58+00', '2022-06-30 07:31:58+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (90, 90, NULL, 155, '2022-06-30 07:31:58+00', '2022-06-30 07:31:58+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (91, 91, NULL, 2, '2022-06-30 07:31:58+00', '2022-06-30 07:31:58+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (92, 92, NULL, 983, '2022-06-30 07:31:58+00', '2022-06-30 07:31:58+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (93, 93, NULL, 375, '2022-06-30 07:31:58+00', '2022-06-30 07:31:58+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (94, 94, NULL, 268, '2022-06-30 07:31:58+00', '2022-06-30 07:31:58+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (95, 95, NULL, 827, '2022-06-30 07:31:58+00', '2022-06-30 07:31:58+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (96, 96, NULL, 870, '2022-06-30 07:31:58+00', '2022-06-30 07:31:58+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (97, 97, NULL, 104, '2022-06-30 07:31:58+00', '2022-06-30 07:31:58+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (98, 98, NULL, 657, '2022-06-30 07:31:58+00', '2022-06-30 07:31:58+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (99, 99, NULL, 378, '2022-06-30 07:31:58+00', '2022-06-30 07:31:58+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (100, 100, NULL, 138, '2022-06-30 07:31:58+00', '2022-06-30 07:31:58+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (101, 101, NULL, 250, '2022-06-30 07:31:58+00', '2022-06-30 07:31:58+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (102, 102, NULL, 568, '2022-06-30 07:31:58+00', '2022-06-30 07:31:58+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (103, 103, NULL, 699, '2022-06-30 07:31:58+00', '2022-06-30 07:31:58+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (104, 104, NULL, 497, '2022-06-30 07:31:58+00', '2022-06-30 07:31:59+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (105, 105, NULL, 848, '2022-06-30 07:31:59+00', '2022-06-30 07:31:59+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (106, 106, NULL, 811, '2022-06-30 07:31:59+00', '2022-06-30 07:31:59+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (107, 107, NULL, 816, '2022-06-30 07:31:59+00', '2022-06-30 07:31:59+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (108, 108, NULL, 578, '2022-06-30 07:31:59+00', '2022-06-30 07:31:59+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (109, 109, NULL, 631, '2022-06-30 07:31:59+00', '2022-06-30 07:31:59+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (110, 110, NULL, 839, '2022-06-30 07:31:59+00', '2022-06-30 07:31:59+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (111, 111, NULL, 218, '2022-06-30 07:31:59+00', '2022-06-30 07:31:59+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (112, 112, NULL, 491, '2022-06-30 07:31:59+00', '2022-06-30 07:31:59+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (113, 113, NULL, 529, '2022-06-30 07:31:59+00', '2022-06-30 07:31:59+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (114, 114, NULL, 160, '2022-06-30 07:31:59+00', '2022-06-30 07:31:59+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (115, 115, NULL, 282, '2022-06-30 07:31:59+00', '2022-06-30 07:31:59+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (116, 116, NULL, 596, '2022-06-30 07:31:59+00', '2022-06-30 07:31:59+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (117, 117, NULL, 196, '2022-06-30 07:31:59+00', '2022-06-30 07:31:59+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (118, 118, NULL, 457, '2022-06-30 07:31:59+00', '2022-06-30 07:31:59+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (119, 119, NULL, 160, '2022-06-30 07:31:59+00', '2022-06-30 07:31:59+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (120, 120, NULL, 760, '2022-06-30 07:32:00+00', '2022-06-30 07:32:00+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (121, 121, NULL, 463, '2022-06-30 07:32:00+00', '2022-06-30 07:32:00+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (122, 122, NULL, 768, '2022-06-30 07:32:00+00', '2022-06-30 07:32:00+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (123, 123, NULL, 424, '2022-06-30 07:32:00+00', '2022-06-30 07:32:00+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (124, 124, NULL, 532, '2022-06-30 07:32:00+00', '2022-06-30 07:32:00+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (125, 125, NULL, 479, '2022-06-30 07:32:00+00', '2022-06-30 07:32:00+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (126, 126, NULL, 913, '2022-06-30 07:32:00+00', '2022-06-30 07:32:00+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (127, 127, NULL, 420, '2022-06-30 07:32:00+00', '2022-06-30 07:32:00+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (128, 128, NULL, 535, '2022-06-30 07:32:00+00', '2022-06-30 07:32:00+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (129, 129, NULL, 839, '2022-06-30 07:32:00+00', '2022-06-30 07:32:00+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (130, 130, NULL, 370, '2022-06-30 07:32:00+00', '2022-06-30 07:32:00+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (131, 131, NULL, 397, '2022-06-30 07:32:00+00', '2022-06-30 07:32:00+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (132, 132, NULL, 755, '2022-06-30 07:32:00+00', '2022-06-30 07:32:00+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (133, 133, NULL, 506, '2022-06-30 07:32:00+00', '2022-06-30 07:32:00+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (134, 134, NULL, 793, '2022-06-30 07:32:00+00', '2022-06-30 07:32:00+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (135, 135, NULL, 290, '2022-06-30 07:32:00+00', '2022-06-30 07:32:00+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (136, 136, NULL, 778, '2022-06-30 07:32:01+00', '2022-06-30 07:32:01+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (137, 137, NULL, 178, '2022-06-30 07:32:01+00', '2022-06-30 07:32:01+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (138, 138, NULL, 638, '2022-06-30 07:32:01+00', '2022-06-30 07:32:01+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (139, 139, NULL, 911, '2022-06-30 07:32:01+00', '2022-06-30 07:32:01+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (140, 140, NULL, 830, '2022-06-30 07:32:01+00', '2022-06-30 07:32:01+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (141, 141, NULL, 782, '2022-06-30 07:32:01+00', '2022-06-30 07:32:01+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (142, 142, NULL, 616, '2022-06-30 07:32:01+00', '2022-06-30 07:32:01+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (143, 143, NULL, 372, '2022-06-30 07:32:01+00', '2022-06-30 07:32:01+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (144, 144, NULL, 335, '2022-06-30 07:32:01+00', '2022-06-30 07:32:01+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (145, 145, NULL, 813, '2022-06-30 07:32:01+00', '2022-06-30 07:32:01+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (146, 146, NULL, 566, '2022-06-30 07:32:01+00', '2022-06-30 07:32:01+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (147, 147, NULL, 96, '2022-06-30 07:32:01+00', '2022-06-30 07:32:01+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (148, 148, NULL, 203, '2022-06-30 07:32:01+00', '2022-06-30 07:32:02+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (149, 149, NULL, 329, '2022-06-30 07:32:02+00', '2022-06-30 07:32:02+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (150, 150, NULL, 259, '2022-06-30 07:32:02+00', '2022-06-30 07:32:02+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (151, 151, NULL, 527, '2022-06-30 07:32:02+00', '2022-06-30 07:32:02+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (152, 152, NULL, 303, '2022-06-30 07:32:02+00', '2022-06-30 07:32:02+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (153, 153, NULL, 317, '2022-06-30 07:32:02+00', '2022-06-30 07:32:02+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (154, 154, NULL, 297, '2022-06-30 07:32:02+00', '2022-06-30 07:32:02+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (155, 155, NULL, 147, '2022-06-30 07:32:02+00', '2022-06-30 07:32:02+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (156, 156, NULL, 738, '2022-06-30 07:32:02+00', '2022-06-30 07:32:02+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (157, 157, NULL, 403, '2022-06-30 07:32:02+00', '2022-06-30 07:32:02+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (158, 158, NULL, 732, '2022-06-30 07:32:02+00', '2022-06-30 07:32:02+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (159, 159, NULL, 769, '2022-06-30 07:32:02+00', '2022-06-30 07:32:02+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (160, 160, NULL, 683, '2022-06-30 07:32:02+00', '2022-06-30 07:32:02+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (161, 161, NULL, 181, '2022-06-30 07:32:02+00', '2022-06-30 07:32:02+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (162, 162, NULL, 171, '2022-06-30 07:32:02+00', '2022-06-30 07:32:02+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (163, 163, NULL, 749, '2022-06-30 07:32:02+00', '2022-06-30 07:32:02+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (164, 164, NULL, 252, '2022-06-30 07:32:03+00', '2022-06-30 07:32:03+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (165, 165, NULL, 894, '2022-06-30 07:32:03+00', '2022-06-30 07:32:03+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (166, 166, NULL, 390, '2022-06-30 07:32:03+00', '2022-06-30 07:32:03+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (167, 167, NULL, 904, '2022-06-30 07:32:03+00', '2022-06-30 07:32:03+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (168, 168, NULL, 641, '2022-06-30 07:32:03+00', '2022-06-30 07:32:03+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (169, 169, NULL, 926, '2022-06-30 07:32:03+00', '2022-06-30 07:32:03+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (170, 170, NULL, 408, '2022-06-30 07:32:03+00', '2022-06-30 07:32:03+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (171, 171, NULL, 35, '2022-06-30 07:32:03+00', '2022-06-30 07:32:03+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (172, 172, NULL, 235, '2022-06-30 07:32:03+00', '2022-06-30 07:32:03+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (173, 173, NULL, 738, '2022-06-30 07:32:03+00', '2022-06-30 07:32:03+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (174, 174, NULL, 507, '2022-06-30 07:32:03+00', '2022-06-30 07:32:03+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (175, 175, NULL, 10, '2022-06-30 07:32:03+00', '2022-06-30 07:32:03+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (176, 176, NULL, 421, '2022-06-30 07:32:04+00', '2022-06-30 07:32:04+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (177, 177, NULL, 706, '2022-06-30 07:32:04+00', '2022-06-30 07:32:04+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (178, 178, NULL, 809, '2022-06-30 07:32:04+00', '2022-06-30 07:32:04+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (179, 179, NULL, 772, '2022-06-30 07:32:04+00', '2022-06-30 07:32:04+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (180, 180, NULL, 818, '2022-06-30 07:32:04+00', '2022-06-30 07:32:04+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (181, 181, NULL, 552, '2022-06-30 07:32:04+00', '2022-06-30 07:32:04+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (182, 182, NULL, 599, '2022-06-30 07:32:04+00', '2022-06-30 07:32:04+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (183, 183, NULL, 629, '2022-06-30 07:32:04+00', '2022-06-30 07:32:04+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (184, 184, NULL, 683, '2022-06-30 07:32:04+00', '2022-06-30 07:32:04+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (185, 185, NULL, 507, '2022-06-30 07:32:04+00', '2022-06-30 07:32:04+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (186, 186, NULL, 391, '2022-06-30 07:32:04+00', '2022-06-30 07:32:04+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (187, 187, NULL, 650, '2022-06-30 07:32:04+00', '2022-06-30 07:32:04+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (188, 188, NULL, 837, '2022-06-30 07:32:05+00', '2022-06-30 07:32:05+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (189, 189, NULL, 915, '2022-06-30 07:32:05+00', '2022-06-30 07:32:05+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (190, 190, NULL, 516, '2022-06-30 07:32:05+00', '2022-06-30 07:32:05+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (191, 191, NULL, 122, '2022-06-30 07:32:05+00', '2022-06-30 07:32:05+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (192, 192, NULL, 260, '2022-06-30 07:32:05+00', '2022-06-30 07:32:05+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (193, 193, NULL, 351, '2022-06-30 07:32:05+00', '2022-06-30 07:32:05+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (194, 194, NULL, 387, '2022-06-30 07:32:05+00', '2022-06-30 07:32:05+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (195, 195, NULL, 508, '2022-06-30 07:32:05+00', '2022-06-30 07:32:05+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (196, 196, NULL, 394, '2022-06-30 07:32:05+00', '2022-06-30 07:32:05+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (197, 197, NULL, 387, '2022-06-30 07:32:05+00', '2022-06-30 07:32:05+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (198, 198, NULL, 765, '2022-06-30 07:32:06+00', '2022-06-30 07:32:06+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (199, 199, NULL, 763, '2022-06-30 07:32:06+00', '2022-06-30 07:32:06+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (200, 200, NULL, 718, '2022-06-30 07:32:06+00', '2022-06-30 07:32:06+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (201, 201, NULL, 659, '2022-06-30 07:32:06+00', '2022-06-30 07:32:06+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (202, 202, NULL, 137, '2022-06-30 07:32:06+00', '2022-06-30 07:32:06+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (203, 203, NULL, 773, '2022-06-30 07:32:06+00', '2022-06-30 07:32:06+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (204, 204, NULL, 836, '2022-06-30 07:32:06+00', '2022-06-30 07:32:06+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (205, 205, NULL, 808, '2022-06-30 07:32:06+00', '2022-06-30 07:32:06+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (206, 206, NULL, 275, '2022-06-30 07:32:06+00', '2022-06-30 07:32:06+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (207, 207, NULL, 764, '2022-06-30 07:32:06+00', '2022-06-30 07:32:06+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (208, 208, NULL, 849, '2022-06-30 07:32:07+00', '2022-06-30 07:32:07+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (209, 209, NULL, 380, '2022-06-30 07:32:07+00', '2022-06-30 07:32:07+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (210, 210, NULL, 285, '2022-06-30 07:32:07+00', '2022-06-30 07:32:07+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (211, 211, NULL, 637, '2022-06-30 07:32:07+00', '2022-06-30 07:32:07+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (212, 212, NULL, 684, '2022-06-30 07:32:07+00', '2022-06-30 07:32:07+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (213, 213, NULL, 936, '2022-06-30 07:32:07+00', '2022-06-30 07:32:07+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (214, 214, NULL, 594, '2022-06-30 07:32:07+00', '2022-06-30 07:32:07+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (215, 215, NULL, 238, '2022-06-30 07:32:07+00', '2022-06-30 07:32:07+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (216, 216, NULL, 738, '2022-06-30 07:32:07+00', '2022-06-30 07:32:07+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (217, 217, NULL, 710, '2022-06-30 07:32:07+00', '2022-06-30 07:32:07+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (218, 218, NULL, 534, '2022-06-30 07:32:07+00', '2022-06-30 07:32:07+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (219, 219, NULL, 998, '2022-06-30 07:32:07+00', '2022-06-30 07:32:07+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (220, 220, NULL, 784, '2022-06-30 07:32:08+00', '2022-06-30 07:32:08+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (221, 221, NULL, 954, '2022-06-30 07:32:08+00', '2022-06-30 07:32:08+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (222, 222, NULL, 491, '2022-06-30 07:32:08+00', '2022-06-30 07:32:08+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (223, 223, NULL, 142, '2022-06-30 07:32:08+00', '2022-06-30 07:32:08+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (224, 224, NULL, 300, '2022-06-30 07:32:08+00', '2022-06-30 07:32:08+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (225, 225, NULL, 733, '2022-06-30 07:32:08+00', '2022-06-30 07:32:08+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (226, 226, NULL, 843, '2022-06-30 07:32:08+00', '2022-06-30 07:32:08+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (227, 227, NULL, 44, '2022-06-30 07:32:08+00', '2022-06-30 07:32:08+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (228, 228, NULL, 814, '2022-06-30 07:32:09+00', '2022-06-30 07:32:09+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (229, 229, NULL, 748, '2022-06-30 07:32:09+00', '2022-06-30 07:32:09+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (230, 230, NULL, 329, '2022-06-30 07:32:09+00', '2022-06-30 07:32:09+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (231, 231, NULL, 771, '2022-06-30 07:32:09+00', '2022-06-30 07:32:09+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (232, 232, NULL, 516, '2022-06-30 07:32:09+00', '2022-06-30 07:32:09+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (233, 233, NULL, 940, '2022-06-30 07:32:09+00', '2022-06-30 07:32:09+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (234, 234, NULL, 342, '2022-06-30 07:32:09+00', '2022-06-30 07:32:09+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (235, 235, NULL, 738, '2022-06-30 07:32:09+00', '2022-06-30 07:32:09+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (236, 236, NULL, 257, '2022-06-30 07:32:09+00', '2022-06-30 07:32:09+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (237, 237, NULL, 891, '2022-06-30 07:32:09+00', '2022-06-30 07:32:09+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (238, 238, NULL, 297, '2022-06-30 07:32:09+00', '2022-06-30 07:32:09+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (239, 239, NULL, 268, '2022-06-30 07:32:09+00', '2022-06-30 07:32:10+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (240, 240, NULL, 631, '2022-06-30 07:32:10+00', '2022-06-30 07:32:10+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (241, 241, NULL, 913, '2022-06-30 07:32:10+00', '2022-06-30 07:32:10+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (242, 242, NULL, 501, '2022-06-30 07:32:10+00', '2022-06-30 07:32:10+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (243, 243, NULL, 460, '2022-06-30 07:32:10+00', '2022-06-30 07:32:10+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (244, 244, NULL, 158, '2022-06-30 07:32:10+00', '2022-06-30 07:32:10+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (245, 245, NULL, 433, '2022-06-30 07:32:10+00', '2022-06-30 07:32:10+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (246, 246, NULL, 792, '2022-06-30 07:32:10+00', '2022-06-30 07:32:10+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (247, 247, NULL, 533, '2022-06-30 07:32:10+00', '2022-06-30 07:32:10+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (248, 248, NULL, 869, '2022-06-30 07:32:11+00', '2022-06-30 07:32:11+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (249, 249, NULL, 922, '2022-06-30 07:32:11+00', '2022-06-30 07:32:11+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (250, 250, NULL, 540, '2022-06-30 07:32:11+00', '2022-06-30 07:32:11+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (251, 251, NULL, 656, '2022-06-30 07:32:11+00', '2022-06-30 07:32:11+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (252, 252, NULL, 779, '2022-06-30 07:32:11+00', '2022-06-30 07:32:11+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (253, 253, NULL, 178, '2022-06-30 07:32:11+00', '2022-06-30 07:32:11+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (254, 254, NULL, 605, '2022-06-30 07:32:11+00', '2022-06-30 07:32:11+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (255, 255, NULL, 134, '2022-06-30 07:32:11+00', '2022-06-30 07:32:11+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (256, 256, NULL, 239, '2022-06-30 07:32:11+00', '2022-06-30 07:32:12+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (257, 257, NULL, 938, '2022-06-30 07:32:12+00', '2022-06-30 07:32:12+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (258, 258, NULL, 107, '2022-06-30 07:32:12+00', '2022-06-30 07:32:12+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (259, 259, NULL, 462, '2022-06-30 07:32:12+00', '2022-06-30 07:32:12+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (260, 260, NULL, 585, '2022-06-30 07:32:12+00', '2022-06-30 07:32:12+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (261, 261, NULL, 352, '2022-06-30 07:32:12+00', '2022-06-30 07:32:12+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (262, 262, NULL, 554, '2022-06-30 07:32:12+00', '2022-06-30 07:32:12+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (263, 263, NULL, 456, '2022-06-30 07:32:12+00', '2022-06-30 07:32:12+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (264, 264, NULL, 815, '2022-06-30 07:32:12+00', '2022-06-30 07:32:12+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (265, 265, NULL, 751, '2022-06-30 07:32:12+00', '2022-06-30 07:32:12+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (266, 266, NULL, 107, '2022-06-30 07:32:12+00', '2022-06-30 07:32:12+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (267, 267, NULL, 129, '2022-06-30 07:32:12+00', '2022-06-30 07:32:13+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (268, 268, NULL, 629, '2022-06-30 07:32:13+00', '2022-06-30 07:32:13+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (269, 269, NULL, 972, '2022-06-30 07:32:13+00', '2022-06-30 07:32:13+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (270, 270, NULL, 495, '2022-06-30 07:32:13+00', '2022-06-30 07:32:13+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (271, 271, NULL, 561, '2022-06-30 07:32:13+00', '2022-06-30 07:32:13+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (272, 272, NULL, 113, '2022-06-30 07:32:13+00', '2022-06-30 07:32:13+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (273, 273, NULL, 273, '2022-06-30 07:32:13+00', '2022-06-30 07:32:13+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (274, 274, NULL, 436, '2022-06-30 07:32:13+00', '2022-06-30 07:32:13+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (275, 275, NULL, 37, '2022-06-30 07:32:13+00', '2022-06-30 07:32:13+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (276, 276, NULL, 159, '2022-06-30 07:32:14+00', '2022-06-30 07:32:14+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (277, 277, NULL, 980, '2022-06-30 07:32:14+00', '2022-06-30 07:32:14+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (278, 278, NULL, 116, '2022-06-30 07:32:14+00', '2022-06-30 07:32:14+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (279, 279, NULL, 268, '2022-06-30 07:32:14+00', '2022-06-30 07:32:14+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (280, 280, NULL, 723, '2022-06-30 07:32:14+00', '2022-06-30 07:32:14+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (281, 281, NULL, 778, '2022-06-30 07:32:14+00', '2022-06-30 07:32:14+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (282, 282, NULL, 462, '2022-06-30 07:32:14+00', '2022-06-30 07:32:14+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (283, 283, NULL, 943, '2022-06-30 07:32:14+00', '2022-06-30 07:32:14+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (284, 284, NULL, 144, '2022-06-30 07:32:15+00', '2022-06-30 07:32:15+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (285, 285, NULL, 632, '2022-06-30 07:32:15+00', '2022-06-30 07:32:15+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (286, 286, NULL, 472, '2022-06-30 07:32:15+00', '2022-06-30 07:32:15+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (287, 287, NULL, 754, '2022-06-30 07:32:15+00', '2022-06-30 07:32:15+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (288, 288, NULL, 943, '2022-06-30 07:32:15+00', '2022-06-30 07:32:15+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (289, 289, NULL, 810, '2022-06-30 07:32:15+00', '2022-06-30 07:32:15+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (290, 290, NULL, 886, '2022-06-30 07:32:15+00', '2022-06-30 07:32:15+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (291, 291, NULL, 214, '2022-06-30 07:32:15+00', '2022-06-30 07:32:15+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (292, 292, NULL, 685, '2022-06-30 07:32:16+00', '2022-06-30 07:32:16+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (293, 293, NULL, 837, '2022-06-30 07:32:16+00', '2022-06-30 07:32:16+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (294, 294, NULL, 966, '2022-06-30 07:32:16+00', '2022-06-30 07:32:16+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (295, 295, NULL, 130, '2022-06-30 07:32:16+00', '2022-06-30 07:32:16+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (296, 296, NULL, 690, '2022-06-30 07:32:16+00', '2022-06-30 07:32:16+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (297, 297, NULL, 670, '2022-06-30 07:32:16+00', '2022-06-30 07:32:16+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (298, 298, NULL, 434, '2022-06-30 07:32:16+00', '2022-06-30 07:32:16+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (299, 299, NULL, 795, '2022-06-30 07:32:16+00', '2022-06-30 07:32:16+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (300, 300, NULL, 555, '2022-06-30 07:32:17+00', '2022-06-30 07:32:17+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (301, 301, NULL, 973, '2022-06-30 07:32:17+00', '2022-06-30 07:32:17+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (302, 302, NULL, 766, '2022-06-30 07:32:17+00', '2022-06-30 07:32:17+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (303, 303, NULL, 459, '2022-06-30 07:32:17+00', '2022-06-30 07:32:17+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (304, 304, NULL, 720, '2022-06-30 07:32:17+00', '2022-06-30 07:32:17+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (305, 305, NULL, 788, '2022-06-30 07:32:17+00', '2022-06-30 07:32:17+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (306, 306, NULL, 405, '2022-06-30 07:32:17+00', '2022-06-30 07:32:17+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (307, 307, NULL, 83, '2022-06-30 07:32:17+00', '2022-06-30 07:32:17+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (308, 308, NULL, 463, '2022-06-30 07:32:18+00', '2022-06-30 07:32:18+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (309, 309, NULL, 624, '2022-06-30 07:32:18+00', '2022-06-30 07:32:18+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (310, 310, NULL, 935, '2022-06-30 07:32:18+00', '2022-06-30 07:32:18+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (311, 311, NULL, 623, '2022-06-30 07:32:18+00', '2022-06-30 07:32:18+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (312, 312, NULL, 916, '2022-06-30 07:32:18+00', '2022-06-30 07:32:18+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (313, 313, NULL, 605, '2022-06-30 07:32:18+00', '2022-06-30 07:32:18+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (314, 314, NULL, 682, '2022-06-30 07:32:18+00', '2022-06-30 07:32:18+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (315, 315, NULL, 953, '2022-06-30 07:32:18+00', '2022-06-30 07:32:18+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (316, 316, NULL, 878, '2022-06-30 07:32:19+00', '2022-06-30 07:32:19+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (317, 317, NULL, 390, '2022-06-30 07:32:19+00', '2022-06-30 07:32:19+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (318, 318, NULL, 451, '2022-06-30 07:32:19+00', '2022-06-30 07:32:19+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (319, 319, NULL, 314, '2022-06-30 07:32:19+00', '2022-06-30 07:32:19+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (320, 320, NULL, 568, '2022-06-30 07:32:19+00', '2022-06-30 07:32:19+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (321, 321, NULL, 736, '2022-06-30 07:32:19+00', '2022-06-30 07:32:19+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (322, 322, NULL, 981, '2022-06-30 07:32:19+00', '2022-06-30 07:32:19+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (323, 323, NULL, 218, '2022-06-30 07:32:19+00', '2022-06-30 07:32:19+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (324, 324, NULL, 309, '2022-06-30 07:32:20+00', '2022-06-30 07:32:20+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (325, 325, NULL, 367, '2022-06-30 07:32:20+00', '2022-06-30 07:32:20+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (326, 326, NULL, 130, '2022-06-30 07:32:20+00', '2022-06-30 07:32:20+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (327, 327, NULL, 126, '2022-06-30 07:32:20+00', '2022-06-30 07:32:20+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (328, 328, NULL, 454, '2022-06-30 07:32:20+00', '2022-06-30 07:32:20+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (329, 329, NULL, 569, '2022-06-30 07:32:20+00', '2022-06-30 07:32:20+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (330, 330, NULL, 414, '2022-06-30 07:32:20+00', '2022-06-30 07:32:20+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (331, 331, NULL, 702, '2022-06-30 07:32:20+00', '2022-06-30 07:32:21+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (332, 332, NULL, 755, '2022-06-30 07:32:21+00', '2022-06-30 07:32:21+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (333, 333, NULL, 194, '2022-06-30 07:32:21+00', '2022-06-30 07:32:21+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (334, 334, NULL, 368, '2022-06-30 07:32:21+00', '2022-06-30 07:32:21+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (335, 335, NULL, 771, '2022-06-30 07:32:21+00', '2022-06-30 07:32:21+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (336, 336, NULL, 546, '2022-06-30 07:32:21+00', '2022-06-30 07:32:21+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (337, 337, NULL, 285, '2022-06-30 07:32:21+00', '2022-06-30 07:32:22+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (338, 338, NULL, 731, '2022-06-30 07:32:22+00', '2022-06-30 07:32:22+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (339, 339, NULL, 917, '2022-06-30 07:32:22+00', '2022-06-30 07:32:22+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (340, 340, NULL, 526, '2022-06-30 07:32:22+00', '2022-06-30 07:32:22+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (341, 341, NULL, 312, '2022-06-30 07:32:22+00', '2022-06-30 07:32:22+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (342, 342, NULL, 113, '2022-06-30 07:32:22+00', '2022-06-30 07:32:22+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (343, 343, NULL, 881, '2022-06-30 07:32:22+00', '2022-06-30 07:32:22+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (344, 344, NULL, 326, '2022-06-30 07:32:23+00', '2022-06-30 07:32:23+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (345, 345, NULL, 262, '2022-06-30 07:32:23+00', '2022-06-30 07:32:23+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (346, 346, NULL, 791, '2022-06-30 07:32:23+00', '2022-06-30 07:32:23+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (347, 347, NULL, 62, '2022-06-30 07:32:23+00', '2022-06-30 07:32:23+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (348, 348, NULL, 961, '2022-06-30 07:32:23+00', '2022-06-30 07:32:23+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (349, 349, NULL, 223, '2022-06-30 07:32:23+00', '2022-06-30 07:32:23+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (350, 350, NULL, 589, '2022-06-30 07:32:23+00', '2022-06-30 07:32:23+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (351, 351, NULL, 612, '2022-06-30 07:32:23+00', '2022-06-30 07:32:23+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (352, 352, NULL, 466, '2022-06-30 07:32:24+00', '2022-06-30 07:32:24+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (353, 353, NULL, 774, '2022-06-30 07:32:24+00', '2022-06-30 07:32:24+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (354, 354, NULL, 961, '2022-06-30 07:32:24+00', '2022-06-30 07:32:24+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (355, 355, NULL, 375, '2022-06-30 07:32:24+00', '2022-06-30 07:32:24+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (356, 356, NULL, 794, '2022-06-30 07:32:24+00', '2022-06-30 07:32:24+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (357, 357, NULL, 781, '2022-06-30 07:32:24+00', '2022-06-30 07:32:24+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (358, 358, NULL, 304, '2022-06-30 07:32:24+00', '2022-06-30 07:32:24+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (359, 359, NULL, 1, '2022-06-30 07:32:24+00', '2022-06-30 07:32:25+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (360, 360, NULL, 515, '2022-06-30 07:32:25+00', '2022-06-30 07:32:25+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (361, 361, NULL, 468, '2022-06-30 07:32:25+00', '2022-06-30 07:32:25+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (362, 362, NULL, 445, '2022-06-30 07:32:25+00', '2022-06-30 07:32:25+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (363, 363, NULL, 260, '2022-06-30 07:32:25+00', '2022-06-30 07:32:25+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (364, 364, NULL, 671, '2022-06-30 07:32:25+00', '2022-06-30 07:32:26+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (365, 365, NULL, 999, '2022-06-30 07:32:26+00', '2022-06-30 07:32:26+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (366, 366, NULL, 719, '2022-06-30 07:32:26+00', '2022-06-30 07:32:26+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (367, 367, NULL, 105, '2022-06-30 07:32:26+00', '2022-06-30 07:32:26+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (368, 368, NULL, 926, '2022-06-30 07:32:26+00', '2022-06-30 07:32:26+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (369, 369, NULL, 581, '2022-06-30 07:32:26+00', '2022-06-30 07:32:26+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (370, 370, NULL, 716, '2022-06-30 07:32:26+00', '2022-06-30 07:32:26+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (371, 371, NULL, 837, '2022-06-30 07:32:26+00', '2022-06-30 07:32:26+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (372, 372, NULL, 980, '2022-06-30 07:32:27+00', '2022-06-30 07:32:27+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (373, 373, NULL, 608, '2022-06-30 07:32:27+00', '2022-06-30 07:32:27+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (374, 374, NULL, 319, '2022-06-30 07:32:27+00', '2022-06-30 07:32:27+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (375, 375, NULL, 148, '2022-06-30 07:32:27+00', '2022-06-30 07:32:27+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (376, 376, NULL, 270, '2022-06-30 07:32:27+00', '2022-06-30 07:32:27+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (377, 377, NULL, 729, '2022-06-30 07:32:27+00', '2022-06-30 07:32:27+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (378, 378, NULL, 830, '2022-06-30 07:32:27+00', '2022-06-30 07:32:27+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (379, 379, NULL, 689, '2022-06-30 07:32:27+00', '2022-06-30 07:32:28+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (380, 380, NULL, 660, '2022-06-30 07:32:28+00', '2022-06-30 07:32:28+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (381, 381, NULL, 916, '2022-06-30 07:32:28+00', '2022-06-30 07:32:28+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (382, 382, NULL, 456, '2022-06-30 07:32:28+00', '2022-06-30 07:32:28+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (383, 383, NULL, 525, '2022-06-30 07:32:28+00', '2022-06-30 07:32:28+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (384, 384, NULL, 647, '2022-06-30 07:32:28+00', '2022-06-30 07:32:29+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (385, 385, NULL, 557, '2022-06-30 07:32:29+00', '2022-06-30 07:32:29+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (386, 386, NULL, 687, '2022-06-30 07:32:29+00', '2022-06-30 07:32:29+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (387, 387, NULL, 225, '2022-06-30 07:32:29+00', '2022-06-30 07:32:29+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (388, 388, NULL, 824, '2022-06-30 07:32:29+00', '2022-06-30 07:32:29+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (389, 389, NULL, 721, '2022-06-30 07:32:29+00', '2022-06-30 07:32:29+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (390, 390, NULL, 141, '2022-06-30 07:32:29+00', '2022-06-30 07:32:29+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (391, 391, NULL, 23, '2022-06-30 07:32:29+00', '2022-06-30 07:32:29+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (392, 392, NULL, 152, '2022-06-30 07:32:30+00', '2022-06-30 07:32:30+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (393, 393, NULL, 360, '2022-06-30 07:32:30+00', '2022-06-30 07:32:30+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (394, 394, NULL, 933, '2022-06-30 07:32:30+00', '2022-06-30 07:32:30+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (395, 395, NULL, 968, '2022-06-30 07:32:30+00', '2022-06-30 07:32:30+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (396, 396, NULL, 991, '2022-06-30 07:32:30+00', '2022-06-30 07:32:30+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (397, 397, NULL, 940, '2022-06-30 07:32:30+00', '2022-06-30 07:32:31+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (398, 398, NULL, 642, '2022-06-30 07:32:31+00', '2022-06-30 07:32:31+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (399, 399, NULL, 672, '2022-06-30 07:32:31+00', '2022-06-30 07:32:31+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (400, 400, NULL, 288, '2022-06-30 07:32:31+00', '2022-06-30 07:32:31+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (401, 401, NULL, 557, '2022-06-30 07:32:31+00', '2022-06-30 07:32:31+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (402, 402, NULL, 575, '2022-06-30 07:32:31+00', '2022-06-30 07:32:31+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (403, 403, NULL, 303, '2022-06-30 07:32:31+00', '2022-06-30 07:32:31+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (404, 404, NULL, 636, '2022-06-30 07:32:32+00', '2022-06-30 07:32:32+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (405, 405, NULL, 780, '2022-06-30 07:32:32+00', '2022-06-30 07:32:32+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (406, 406, NULL, 420, '2022-06-30 07:32:32+00', '2022-06-30 07:32:32+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (407, 407, NULL, 206, '2022-06-30 07:32:32+00', '2022-06-30 07:32:32+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (408, 408, NULL, 928, '2022-06-30 07:32:32+00', '2022-06-30 07:32:32+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (409, 409, NULL, 925, '2022-06-30 07:32:32+00', '2022-06-30 07:32:32+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (410, 410, NULL, 608, '2022-06-30 07:32:32+00', '2022-06-30 07:32:32+00', 'productstock');
INSERT INTO public.dtb_product_stock VALUES (411, 411, NULL, 441, '2022-06-30 07:32:32+00', '2022-06-30 07:32:33+00', 'productstock');


--
-- Data for Name: dtb_product_tag; Type: TABLE DATA; Schema: public; Owner: dbuser
--

INSERT INTO public.dtb_product_tag VALUES (1, 3, 1, NULL, '2022-06-30 07:31:54+00', 'producttag');
INSERT INTO public.dtb_product_tag VALUES (2, 3, 2, NULL, '2022-06-30 07:31:54+00', 'producttag');
INSERT INTO public.dtb_product_tag VALUES (3, 3, 3, NULL, '2022-06-30 07:31:55+00', 'producttag');
INSERT INTO public.dtb_product_tag VALUES (4, 4, 1, NULL, '2022-06-30 07:31:55+00', 'producttag');
INSERT INTO public.dtb_product_tag VALUES (5, 4, 2, NULL, '2022-06-30 07:31:55+00', 'producttag');
INSERT INTO public.dtb_product_tag VALUES (6, 4, 3, NULL, '2022-06-30 07:31:55+00', 'producttag');
INSERT INTO public.dtb_product_tag VALUES (7, 5, 1, NULL, '2022-06-30 07:31:55+00', 'producttag');
INSERT INTO public.dtb_product_tag VALUES (8, 5, 2, NULL, '2022-06-30 07:31:55+00', 'producttag');
INSERT INTO public.dtb_product_tag VALUES (9, 5, 3, NULL, '2022-06-30 07:31:55+00', 'producttag');
INSERT INTO public.dtb_product_tag VALUES (10, 6, 1, NULL, '2022-06-30 07:31:55+00', 'producttag');
INSERT INTO public.dtb_product_tag VALUES (11, 6, 2, NULL, '2022-06-30 07:31:55+00', 'producttag');
INSERT INTO public.dtb_product_tag VALUES (12, 6, 3, NULL, '2022-06-30 07:31:55+00', 'producttag');
INSERT INTO public.dtb_product_tag VALUES (13, 7, 1, NULL, '2022-06-30 07:31:55+00', 'producttag');
INSERT INTO public.dtb_product_tag VALUES (14, 7, 2, NULL, '2022-06-30 07:31:55+00', 'producttag');
INSERT INTO public.dtb_product_tag VALUES (15, 7, 3, NULL, '2022-06-30 07:31:55+00', 'producttag');
INSERT INTO public.dtb_product_tag VALUES (16, 8, 1, NULL, '2022-06-30 07:31:55+00', 'producttag');
INSERT INTO public.dtb_product_tag VALUES (17, 8, 2, NULL, '2022-06-30 07:31:55+00', 'producttag');
INSERT INTO public.dtb_product_tag VALUES (18, 8, 3, NULL, '2022-06-30 07:31:55+00', 'producttag');
INSERT INTO public.dtb_product_tag VALUES (19, 9, 1, NULL, '2022-06-30 07:31:55+00', 'producttag');
INSERT INTO public.dtb_product_tag VALUES (20, 9, 2, NULL, '2022-06-30 07:31:55+00', 'producttag');
INSERT INTO public.dtb_product_tag VALUES (21, 9, 3, NULL, '2022-06-30 07:31:55+00', 'producttag');
INSERT INTO public.dtb_product_tag VALUES (22, 10, 1, NULL, '2022-06-30 07:31:55+00', 'producttag');
INSERT INTO public.dtb_product_tag VALUES (23, 10, 2, NULL, '2022-06-30 07:31:55+00', 'producttag');
INSERT INTO public.dtb_product_tag VALUES (24, 10, 3, NULL, '2022-06-30 07:31:55+00', 'producttag');
INSERT INTO public.dtb_product_tag VALUES (25, 11, 1, NULL, '2022-06-30 07:31:56+00', 'producttag');
INSERT INTO public.dtb_product_tag VALUES (26, 11, 2, NULL, '2022-06-30 07:31:56+00', 'producttag');
INSERT INTO public.dtb_product_tag VALUES (27, 11, 3, NULL, '2022-06-30 07:31:56+00', 'producttag');
INSERT INTO public.dtb_product_tag VALUES (28, 12, 1, NULL, '2022-06-30 07:31:56+00', 'producttag');
INSERT INTO public.dtb_product_tag VALUES (29, 12, 2, NULL, '2022-06-30 07:31:56+00', 'producttag');
INSERT INTO public.dtb_product_tag VALUES (30, 12, 3, NULL, '2022-06-30 07:31:56+00', 'producttag');
INSERT INTO public.dtb_product_tag VALUES (31, 13, 1, NULL, '2022-06-30 07:31:56+00', 'producttag');
INSERT INTO public.dtb_product_tag VALUES (32, 13, 2, NULL, '2022-06-30 07:31:56+00', 'producttag');
INSERT INTO public.dtb_product_tag VALUES (33, 13, 3, NULL, '2022-06-30 07:31:56+00', 'producttag');
INSERT INTO public.dtb_product_tag VALUES (34, 14, 1, NULL, '2022-06-30 07:31:56+00', 'producttag');
INSERT INTO public.dtb_product_tag VALUES (35, 14, 2, NULL, '2022-06-30 07:31:56+00', 'producttag');
INSERT INTO public.dtb_product_tag VALUES (36, 14, 3, NULL, '2022-06-30 07:31:56+00', 'producttag');
INSERT INTO public.dtb_product_tag VALUES (37, 15, 1, NULL, '2022-06-30 07:31:56+00', 'producttag');
INSERT INTO public.dtb_product_tag VALUES (38, 15, 2, NULL, '2022-06-30 07:31:56+00', 'producttag');
INSERT INTO public.dtb_product_tag VALUES (39, 15, 3, NULL, '2022-06-30 07:31:56+00', 'producttag');
INSERT INTO public.dtb_product_tag VALUES (40, 16, 1, NULL, '2022-06-30 07:31:56+00', 'producttag');
INSERT INTO public.dtb_product_tag VALUES (41, 16, 2, NULL, '2022-06-30 07:31:56+00', 'producttag');
INSERT INTO public.dtb_product_tag VALUES (42, 16, 3, NULL, '2022-06-30 07:31:56+00', 'producttag');
INSERT INTO public.dtb_product_tag VALUES (43, 17, 1, NULL, '2022-06-30 07:31:57+00', 'producttag');
INSERT INTO public.dtb_product_tag VALUES (44, 17, 2, NULL, '2022-06-30 07:31:57+00', 'producttag');
INSERT INTO public.dtb_product_tag VALUES (45, 17, 3, NULL, '2022-06-30 07:31:57+00', 'producttag');
INSERT INTO public.dtb_product_tag VALUES (46, 18, 1, NULL, '2022-06-30 07:31:57+00', 'producttag');
INSERT INTO public.dtb_product_tag VALUES (47, 18, 2, NULL, '2022-06-30 07:31:57+00', 'producttag');
INSERT INTO public.dtb_product_tag VALUES (48, 18, 3, NULL, '2022-06-30 07:31:57+00', 'producttag');
INSERT INTO public.dtb_product_tag VALUES (49, 19, 1, NULL, '2022-06-30 07:31:57+00', 'producttag');
INSERT INTO public.dtb_product_tag VALUES (50, 19, 2, NULL, '2022-06-30 07:31:57+00', 'producttag');
INSERT INTO public.dtb_product_tag VALUES (51, 19, 3, NULL, '2022-06-30 07:31:57+00', 'producttag');
INSERT INTO public.dtb_product_tag VALUES (52, 20, 1, NULL, '2022-06-30 07:31:57+00', 'producttag');
INSERT INTO public.dtb_product_tag VALUES (53, 20, 2, NULL, '2022-06-30 07:31:57+00', 'producttag');
INSERT INTO public.dtb_product_tag VALUES (54, 20, 3, NULL, '2022-06-30 07:31:57+00', 'producttag');
INSERT INTO public.dtb_product_tag VALUES (55, 21, 1, NULL, '2022-06-30 07:31:57+00', 'producttag');
INSERT INTO public.dtb_product_tag VALUES (56, 21, 2, NULL, '2022-06-30 07:31:57+00', 'producttag');
INSERT INTO public.dtb_product_tag VALUES (57, 21, 3, NULL, '2022-06-30 07:31:57+00', 'producttag');
INSERT INTO public.dtb_product_tag VALUES (58, 22, 1, NULL, '2022-06-30 07:31:58+00', 'producttag');
INSERT INTO public.dtb_product_tag VALUES (59, 22, 2, NULL, '2022-06-30 07:31:58+00', 'producttag');
INSERT INTO public.dtb_product_tag VALUES (60, 22, 3, NULL, '2022-06-30 07:31:58+00', 'producttag');
INSERT INTO public.dtb_product_tag VALUES (61, 23, 1, NULL, '2022-06-30 07:31:58+00', 'producttag');
INSERT INTO public.dtb_product_tag VALUES (62, 23, 2, NULL, '2022-06-30 07:31:58+00', 'producttag');
INSERT INTO public.dtb_product_tag VALUES (63, 23, 3, NULL, '2022-06-30 07:31:58+00', 'producttag');
INSERT INTO public.dtb_product_tag VALUES (64, 24, 1, NULL, '2022-06-30 07:31:58+00', 'producttag');
INSERT INTO public.dtb_product_tag VALUES (65, 24, 2, NULL, '2022-06-30 07:31:58+00', 'producttag');
INSERT INTO public.dtb_product_tag VALUES (66, 24, 3, NULL, '2022-06-30 07:31:58+00', 'producttag');
INSERT INTO public.dtb_product_tag VALUES (67, 25, 1, NULL, '2022-06-30 07:31:58+00', 'producttag');
INSERT INTO public.dtb_product_tag VALUES (68, 25, 2, NULL, '2022-06-30 07:31:58+00', 'producttag');
INSERT INTO public.dtb_product_tag VALUES (69, 25, 3, NULL, '2022-06-30 07:31:58+00', 'producttag');
INSERT INTO public.dtb_product_tag VALUES (70, 26, 1, NULL, '2022-06-30 07:31:59+00', 'producttag');
INSERT INTO public.dtb_product_tag VALUES (71, 26, 2, NULL, '2022-06-30 07:31:59+00', 'producttag');
INSERT INTO public.dtb_product_tag VALUES (72, 26, 3, NULL, '2022-06-30 07:31:59+00', 'producttag');
INSERT INTO public.dtb_product_tag VALUES (73, 27, 1, NULL, '2022-06-30 07:31:59+00', 'producttag');
INSERT INTO public.dtb_product_tag VALUES (74, 27, 2, NULL, '2022-06-30 07:31:59+00', 'producttag');
INSERT INTO public.dtb_product_tag VALUES (75, 27, 3, NULL, '2022-06-30 07:31:59+00', 'producttag');
INSERT INTO public.dtb_product_tag VALUES (76, 28, 1, NULL, '2022-06-30 07:31:59+00', 'producttag');
INSERT INTO public.dtb_product_tag VALUES (77, 28, 2, NULL, '2022-06-30 07:31:59+00', 'producttag');
INSERT INTO public.dtb_product_tag VALUES (78, 28, 3, NULL, '2022-06-30 07:31:59+00', 'producttag');
INSERT INTO public.dtb_product_tag VALUES (79, 29, 1, NULL, '2022-06-30 07:31:59+00', 'producttag');
INSERT INTO public.dtb_product_tag VALUES (80, 29, 2, NULL, '2022-06-30 07:31:59+00', 'producttag');
INSERT INTO public.dtb_product_tag VALUES (81, 29, 3, NULL, '2022-06-30 07:31:59+00', 'producttag');
INSERT INTO public.dtb_product_tag VALUES (82, 30, 1, NULL, '2022-06-30 07:32:00+00', 'producttag');
INSERT INTO public.dtb_product_tag VALUES (83, 30, 2, NULL, '2022-06-30 07:32:00+00', 'producttag');
INSERT INTO public.dtb_product_tag VALUES (84, 30, 3, NULL, '2022-06-30 07:32:00+00', 'producttag');
INSERT INTO public.dtb_product_tag VALUES (85, 31, 1, NULL, '2022-06-30 07:32:00+00', 'producttag');
INSERT INTO public.dtb_product_tag VALUES (86, 31, 2, NULL, '2022-06-30 07:32:00+00', 'producttag');
INSERT INTO public.dtb_product_tag VALUES (87, 31, 3, NULL, '2022-06-30 07:32:00+00', 'producttag');
INSERT INTO public.dtb_product_tag VALUES (88, 32, 1, NULL, '2022-06-30 07:32:00+00', 'producttag');
INSERT INTO public.dtb_product_tag VALUES (89, 32, 2, NULL, '2022-06-30 07:32:00+00', 'producttag');
INSERT INTO public.dtb_product_tag VALUES (90, 32, 3, NULL, '2022-06-30 07:32:00+00', 'producttag');
INSERT INTO public.dtb_product_tag VALUES (91, 33, 1, NULL, '2022-06-30 07:32:01+00', 'producttag');
INSERT INTO public.dtb_product_tag VALUES (92, 33, 2, NULL, '2022-06-30 07:32:01+00', 'producttag');
INSERT INTO public.dtb_product_tag VALUES (93, 33, 3, NULL, '2022-06-30 07:32:01+00', 'producttag');
INSERT INTO public.dtb_product_tag VALUES (94, 34, 1, NULL, '2022-06-30 07:32:01+00', 'producttag');
INSERT INTO public.dtb_product_tag VALUES (95, 34, 2, NULL, '2022-06-30 07:32:01+00', 'producttag');
INSERT INTO public.dtb_product_tag VALUES (96, 34, 3, NULL, '2022-06-30 07:32:01+00', 'producttag');
INSERT INTO public.dtb_product_tag VALUES (97, 35, 1, NULL, '2022-06-30 07:32:01+00', 'producttag');
INSERT INTO public.dtb_product_tag VALUES (98, 35, 2, NULL, '2022-06-30 07:32:01+00', 'producttag');
INSERT INTO public.dtb_product_tag VALUES (99, 35, 3, NULL, '2022-06-30 07:32:01+00', 'producttag');
INSERT INTO public.dtb_product_tag VALUES (100, 36, 1, NULL, '2022-06-30 07:32:01+00', 'producttag');
INSERT INTO public.dtb_product_tag VALUES (101, 36, 2, NULL, '2022-06-30 07:32:01+00', 'producttag');
INSERT INTO public.dtb_product_tag VALUES (102, 36, 3, NULL, '2022-06-30 07:32:01+00', 'producttag');
INSERT INTO public.dtb_product_tag VALUES (103, 37, 1, NULL, '2022-06-30 07:32:02+00', 'producttag');
INSERT INTO public.dtb_product_tag VALUES (104, 37, 2, NULL, '2022-06-30 07:32:02+00', 'producttag');
INSERT INTO public.dtb_product_tag VALUES (105, 37, 3, NULL, '2022-06-30 07:32:02+00', 'producttag');
INSERT INTO public.dtb_product_tag VALUES (106, 38, 1, NULL, '2022-06-30 07:32:02+00', 'producttag');
INSERT INTO public.dtb_product_tag VALUES (107, 38, 2, NULL, '2022-06-30 07:32:02+00', 'producttag');
INSERT INTO public.dtb_product_tag VALUES (108, 38, 3, NULL, '2022-06-30 07:32:02+00', 'producttag');
INSERT INTO public.dtb_product_tag VALUES (109, 39, 1, NULL, '2022-06-30 07:32:02+00', 'producttag');
INSERT INTO public.dtb_product_tag VALUES (110, 39, 2, NULL, '2022-06-30 07:32:02+00', 'producttag');
INSERT INTO public.dtb_product_tag VALUES (111, 39, 3, NULL, '2022-06-30 07:32:02+00', 'producttag');
INSERT INTO public.dtb_product_tag VALUES (112, 40, 1, NULL, '2022-06-30 07:32:03+00', 'producttag');
INSERT INTO public.dtb_product_tag VALUES (113, 40, 2, NULL, '2022-06-30 07:32:03+00', 'producttag');
INSERT INTO public.dtb_product_tag VALUES (114, 40, 3, NULL, '2022-06-30 07:32:03+00', 'producttag');
INSERT INTO public.dtb_product_tag VALUES (115, 41, 1, NULL, '2022-06-30 07:32:03+00', 'producttag');
INSERT INTO public.dtb_product_tag VALUES (116, 41, 2, NULL, '2022-06-30 07:32:03+00', 'producttag');
INSERT INTO public.dtb_product_tag VALUES (117, 41, 3, NULL, '2022-06-30 07:32:03+00', 'producttag');
INSERT INTO public.dtb_product_tag VALUES (118, 42, 1, NULL, '2022-06-30 07:32:03+00', 'producttag');
INSERT INTO public.dtb_product_tag VALUES (119, 42, 2, NULL, '2022-06-30 07:32:03+00', 'producttag');
INSERT INTO public.dtb_product_tag VALUES (120, 42, 3, NULL, '2022-06-30 07:32:03+00', 'producttag');
INSERT INTO public.dtb_product_tag VALUES (121, 43, 1, NULL, '2022-06-30 07:32:04+00', 'producttag');
INSERT INTO public.dtb_product_tag VALUES (122, 43, 2, NULL, '2022-06-30 07:32:04+00', 'producttag');
INSERT INTO public.dtb_product_tag VALUES (123, 43, 3, NULL, '2022-06-30 07:32:04+00', 'producttag');
INSERT INTO public.dtb_product_tag VALUES (124, 44, 1, NULL, '2022-06-30 07:32:04+00', 'producttag');
INSERT INTO public.dtb_product_tag VALUES (125, 44, 2, NULL, '2022-06-30 07:32:04+00', 'producttag');
INSERT INTO public.dtb_product_tag VALUES (126, 44, 3, NULL, '2022-06-30 07:32:04+00', 'producttag');
INSERT INTO public.dtb_product_tag VALUES (127, 45, 1, NULL, '2022-06-30 07:32:04+00', 'producttag');
INSERT INTO public.dtb_product_tag VALUES (128, 45, 2, NULL, '2022-06-30 07:32:04+00', 'producttag');
INSERT INTO public.dtb_product_tag VALUES (129, 45, 3, NULL, '2022-06-30 07:32:04+00', 'producttag');
INSERT INTO public.dtb_product_tag VALUES (130, 46, 1, NULL, '2022-06-30 07:32:05+00', 'producttag');
INSERT INTO public.dtb_product_tag VALUES (131, 46, 2, NULL, '2022-06-30 07:32:05+00', 'producttag');
INSERT INTO public.dtb_product_tag VALUES (132, 46, 3, NULL, '2022-06-30 07:32:05+00', 'producttag');
INSERT INTO public.dtb_product_tag VALUES (133, 47, 1, NULL, '2022-06-30 07:32:05+00', 'producttag');
INSERT INTO public.dtb_product_tag VALUES (134, 47, 2, NULL, '2022-06-30 07:32:05+00', 'producttag');
INSERT INTO public.dtb_product_tag VALUES (135, 47, 3, NULL, '2022-06-30 07:32:05+00', 'producttag');
INSERT INTO public.dtb_product_tag VALUES (136, 48, 1, NULL, '2022-06-30 07:32:05+00', 'producttag');
INSERT INTO public.dtb_product_tag VALUES (137, 48, 2, NULL, '2022-06-30 07:32:05+00', 'producttag');
INSERT INTO public.dtb_product_tag VALUES (138, 48, 3, NULL, '2022-06-30 07:32:05+00', 'producttag');
INSERT INTO public.dtb_product_tag VALUES (139, 49, 1, NULL, '2022-06-30 07:32:06+00', 'producttag');
INSERT INTO public.dtb_product_tag VALUES (140, 49, 2, NULL, '2022-06-30 07:32:06+00', 'producttag');
INSERT INTO public.dtb_product_tag VALUES (141, 49, 3, NULL, '2022-06-30 07:32:06+00', 'producttag');
INSERT INTO public.dtb_product_tag VALUES (142, 50, 1, NULL, '2022-06-30 07:32:06+00', 'producttag');
INSERT INTO public.dtb_product_tag VALUES (143, 50, 2, NULL, '2022-06-30 07:32:06+00', 'producttag');
INSERT INTO public.dtb_product_tag VALUES (144, 50, 3, NULL, '2022-06-30 07:32:06+00', 'producttag');
INSERT INTO public.dtb_product_tag VALUES (145, 51, 1, NULL, '2022-06-30 07:32:06+00', 'producttag');
INSERT INTO public.dtb_product_tag VALUES (146, 51, 2, NULL, '2022-06-30 07:32:06+00', 'producttag');
INSERT INTO public.dtb_product_tag VALUES (147, 51, 3, NULL, '2022-06-30 07:32:06+00', 'producttag');
INSERT INTO public.dtb_product_tag VALUES (148, 52, 1, NULL, '2022-06-30 07:32:07+00', 'producttag');
INSERT INTO public.dtb_product_tag VALUES (149, 52, 2, NULL, '2022-06-30 07:32:07+00', 'producttag');
INSERT INTO public.dtb_product_tag VALUES (150, 52, 3, NULL, '2022-06-30 07:32:07+00', 'producttag');
INSERT INTO public.dtb_product_tag VALUES (151, 53, 1, NULL, '2022-06-30 07:32:07+00', 'producttag');
INSERT INTO public.dtb_product_tag VALUES (152, 53, 2, NULL, '2022-06-30 07:32:07+00', 'producttag');
INSERT INTO public.dtb_product_tag VALUES (153, 53, 3, NULL, '2022-06-30 07:32:07+00', 'producttag');
INSERT INTO public.dtb_product_tag VALUES (154, 54, 1, NULL, '2022-06-30 07:32:08+00', 'producttag');
INSERT INTO public.dtb_product_tag VALUES (155, 54, 2, NULL, '2022-06-30 07:32:08+00', 'producttag');
INSERT INTO public.dtb_product_tag VALUES (156, 54, 3, NULL, '2022-06-30 07:32:08+00', 'producttag');
INSERT INTO public.dtb_product_tag VALUES (157, 55, 1, NULL, '2022-06-30 07:32:08+00', 'producttag');
INSERT INTO public.dtb_product_tag VALUES (158, 55, 2, NULL, '2022-06-30 07:32:08+00', 'producttag');
INSERT INTO public.dtb_product_tag VALUES (159, 55, 3, NULL, '2022-06-30 07:32:08+00', 'producttag');
INSERT INTO public.dtb_product_tag VALUES (160, 56, 1, NULL, '2022-06-30 07:32:08+00', 'producttag');
INSERT INTO public.dtb_product_tag VALUES (161, 56, 2, NULL, '2022-06-30 07:32:08+00', 'producttag');
INSERT INTO public.dtb_product_tag VALUES (162, 56, 3, NULL, '2022-06-30 07:32:08+00', 'producttag');
INSERT INTO public.dtb_product_tag VALUES (163, 57, 1, NULL, '2022-06-30 07:32:09+00', 'producttag');
INSERT INTO public.dtb_product_tag VALUES (164, 57, 2, NULL, '2022-06-30 07:32:09+00', 'producttag');
INSERT INTO public.dtb_product_tag VALUES (165, 57, 3, NULL, '2022-06-30 07:32:09+00', 'producttag');
INSERT INTO public.dtb_product_tag VALUES (166, 58, 1, NULL, '2022-06-30 07:32:09+00', 'producttag');
INSERT INTO public.dtb_product_tag VALUES (167, 58, 2, NULL, '2022-06-30 07:32:09+00', 'producttag');
INSERT INTO public.dtb_product_tag VALUES (168, 58, 3, NULL, '2022-06-30 07:32:09+00', 'producttag');
INSERT INTO public.dtb_product_tag VALUES (169, 59, 1, NULL, '2022-06-30 07:32:10+00', 'producttag');
INSERT INTO public.dtb_product_tag VALUES (170, 59, 2, NULL, '2022-06-30 07:32:10+00', 'producttag');
INSERT INTO public.dtb_product_tag VALUES (171, 59, 3, NULL, '2022-06-30 07:32:10+00', 'producttag');
INSERT INTO public.dtb_product_tag VALUES (172, 60, 1, NULL, '2022-06-30 07:32:10+00', 'producttag');
INSERT INTO public.dtb_product_tag VALUES (173, 60, 2, NULL, '2022-06-30 07:32:10+00', 'producttag');
INSERT INTO public.dtb_product_tag VALUES (174, 60, 3, NULL, '2022-06-30 07:32:10+00', 'producttag');
INSERT INTO public.dtb_product_tag VALUES (175, 61, 1, NULL, '2022-06-30 07:32:10+00', 'producttag');
INSERT INTO public.dtb_product_tag VALUES (176, 61, 2, NULL, '2022-06-30 07:32:10+00', 'producttag');
INSERT INTO public.dtb_product_tag VALUES (177, 61, 3, NULL, '2022-06-30 07:32:10+00', 'producttag');
INSERT INTO public.dtb_product_tag VALUES (178, 62, 1, NULL, '2022-06-30 07:32:11+00', 'producttag');
INSERT INTO public.dtb_product_tag VALUES (179, 62, 2, NULL, '2022-06-30 07:32:11+00', 'producttag');
INSERT INTO public.dtb_product_tag VALUES (180, 62, 3, NULL, '2022-06-30 07:32:11+00', 'producttag');
INSERT INTO public.dtb_product_tag VALUES (181, 63, 1, NULL, '2022-06-30 07:32:11+00', 'producttag');
INSERT INTO public.dtb_product_tag VALUES (182, 63, 2, NULL, '2022-06-30 07:32:11+00', 'producttag');
INSERT INTO public.dtb_product_tag VALUES (183, 63, 3, NULL, '2022-06-30 07:32:11+00', 'producttag');
INSERT INTO public.dtb_product_tag VALUES (184, 64, 1, NULL, '2022-06-30 07:32:12+00', 'producttag');
INSERT INTO public.dtb_product_tag VALUES (185, 64, 2, NULL, '2022-06-30 07:32:12+00', 'producttag');
INSERT INTO public.dtb_product_tag VALUES (186, 64, 3, NULL, '2022-06-30 07:32:12+00', 'producttag');
INSERT INTO public.dtb_product_tag VALUES (187, 65, 1, NULL, '2022-06-30 07:32:12+00', 'producttag');
INSERT INTO public.dtb_product_tag VALUES (188, 65, 2, NULL, '2022-06-30 07:32:12+00', 'producttag');
INSERT INTO public.dtb_product_tag VALUES (189, 65, 3, NULL, '2022-06-30 07:32:12+00', 'producttag');
INSERT INTO public.dtb_product_tag VALUES (190, 66, 1, NULL, '2022-06-30 07:32:13+00', 'producttag');
INSERT INTO public.dtb_product_tag VALUES (191, 66, 2, NULL, '2022-06-30 07:32:13+00', 'producttag');
INSERT INTO public.dtb_product_tag VALUES (192, 66, 3, NULL, '2022-06-30 07:32:13+00', 'producttag');
INSERT INTO public.dtb_product_tag VALUES (193, 67, 1, NULL, '2022-06-30 07:32:13+00', 'producttag');
INSERT INTO public.dtb_product_tag VALUES (194, 67, 2, NULL, '2022-06-30 07:32:13+00', 'producttag');
INSERT INTO public.dtb_product_tag VALUES (195, 67, 3, NULL, '2022-06-30 07:32:13+00', 'producttag');
INSERT INTO public.dtb_product_tag VALUES (196, 68, 1, NULL, '2022-06-30 07:32:14+00', 'producttag');
INSERT INTO public.dtb_product_tag VALUES (197, 68, 2, NULL, '2022-06-30 07:32:14+00', 'producttag');
INSERT INTO public.dtb_product_tag VALUES (198, 68, 3, NULL, '2022-06-30 07:32:14+00', 'producttag');
INSERT INTO public.dtb_product_tag VALUES (199, 69, 1, NULL, '2022-06-30 07:32:14+00', 'producttag');
INSERT INTO public.dtb_product_tag VALUES (200, 69, 2, NULL, '2022-06-30 07:32:14+00', 'producttag');
INSERT INTO public.dtb_product_tag VALUES (201, 69, 3, NULL, '2022-06-30 07:32:14+00', 'producttag');
INSERT INTO public.dtb_product_tag VALUES (202, 70, 1, NULL, '2022-06-30 07:32:14+00', 'producttag');
INSERT INTO public.dtb_product_tag VALUES (203, 70, 2, NULL, '2022-06-30 07:32:15+00', 'producttag');
INSERT INTO public.dtb_product_tag VALUES (204, 70, 3, NULL, '2022-06-30 07:32:15+00', 'producttag');
INSERT INTO public.dtb_product_tag VALUES (205, 71, 1, NULL, '2022-06-30 07:32:15+00', 'producttag');
INSERT INTO public.dtb_product_tag VALUES (206, 71, 2, NULL, '2022-06-30 07:32:15+00', 'producttag');
INSERT INTO public.dtb_product_tag VALUES (207, 71, 3, NULL, '2022-06-30 07:32:15+00', 'producttag');
INSERT INTO public.dtb_product_tag VALUES (208, 72, 1, NULL, '2022-06-30 07:32:15+00', 'producttag');
INSERT INTO public.dtb_product_tag VALUES (209, 72, 2, NULL, '2022-06-30 07:32:15+00', 'producttag');
INSERT INTO public.dtb_product_tag VALUES (210, 72, 3, NULL, '2022-06-30 07:32:15+00', 'producttag');
INSERT INTO public.dtb_product_tag VALUES (211, 73, 1, NULL, '2022-06-30 07:32:16+00', 'producttag');
INSERT INTO public.dtb_product_tag VALUES (212, 73, 2, NULL, '2022-06-30 07:32:16+00', 'producttag');
INSERT INTO public.dtb_product_tag VALUES (213, 73, 3, NULL, '2022-06-30 07:32:16+00', 'producttag');
INSERT INTO public.dtb_product_tag VALUES (214, 74, 1, NULL, '2022-06-30 07:32:16+00', 'producttag');
INSERT INTO public.dtb_product_tag VALUES (215, 74, 2, NULL, '2022-06-30 07:32:16+00', 'producttag');
INSERT INTO public.dtb_product_tag VALUES (216, 74, 3, NULL, '2022-06-30 07:32:16+00', 'producttag');
INSERT INTO public.dtb_product_tag VALUES (217, 75, 1, NULL, '2022-06-30 07:32:17+00', 'producttag');
INSERT INTO public.dtb_product_tag VALUES (218, 75, 2, NULL, '2022-06-30 07:32:17+00', 'producttag');
INSERT INTO public.dtb_product_tag VALUES (219, 75, 3, NULL, '2022-06-30 07:32:17+00', 'producttag');
INSERT INTO public.dtb_product_tag VALUES (220, 76, 1, NULL, '2022-06-30 07:32:17+00', 'producttag');
INSERT INTO public.dtb_product_tag VALUES (221, 76, 2, NULL, '2022-06-30 07:32:17+00', 'producttag');
INSERT INTO public.dtb_product_tag VALUES (222, 76, 3, NULL, '2022-06-30 07:32:17+00', 'producttag');
INSERT INTO public.dtb_product_tag VALUES (223, 77, 1, NULL, '2022-06-30 07:32:18+00', 'producttag');
INSERT INTO public.dtb_product_tag VALUES (224, 77, 2, NULL, '2022-06-30 07:32:18+00', 'producttag');
INSERT INTO public.dtb_product_tag VALUES (225, 77, 3, NULL, '2022-06-30 07:32:18+00', 'producttag');
INSERT INTO public.dtb_product_tag VALUES (226, 78, 1, NULL, '2022-06-30 07:32:19+00', 'producttag');
INSERT INTO public.dtb_product_tag VALUES (227, 78, 2, NULL, '2022-06-30 07:32:19+00', 'producttag');
INSERT INTO public.dtb_product_tag VALUES (228, 78, 3, NULL, '2022-06-30 07:32:19+00', 'producttag');
INSERT INTO public.dtb_product_tag VALUES (229, 79, 1, NULL, '2022-06-30 07:32:19+00', 'producttag');
INSERT INTO public.dtb_product_tag VALUES (230, 79, 2, NULL, '2022-06-30 07:32:19+00', 'producttag');
INSERT INTO public.dtb_product_tag VALUES (231, 79, 3, NULL, '2022-06-30 07:32:19+00', 'producttag');
INSERT INTO public.dtb_product_tag VALUES (232, 80, 1, NULL, '2022-06-30 07:32:20+00', 'producttag');
INSERT INTO public.dtb_product_tag VALUES (233, 80, 2, NULL, '2022-06-30 07:32:20+00', 'producttag');
INSERT INTO public.dtb_product_tag VALUES (234, 80, 3, NULL, '2022-06-30 07:32:20+00', 'producttag');
INSERT INTO public.dtb_product_tag VALUES (235, 81, 1, NULL, '2022-06-30 07:32:20+00', 'producttag');
INSERT INTO public.dtb_product_tag VALUES (236, 81, 2, NULL, '2022-06-30 07:32:20+00', 'producttag');
INSERT INTO public.dtb_product_tag VALUES (237, 81, 3, NULL, '2022-06-30 07:32:20+00', 'producttag');
INSERT INTO public.dtb_product_tag VALUES (238, 82, 1, NULL, '2022-06-30 07:32:21+00', 'producttag');
INSERT INTO public.dtb_product_tag VALUES (239, 82, 2, NULL, '2022-06-30 07:32:21+00', 'producttag');
INSERT INTO public.dtb_product_tag VALUES (240, 82, 3, NULL, '2022-06-30 07:32:21+00', 'producttag');
INSERT INTO public.dtb_product_tag VALUES (241, 83, 1, NULL, '2022-06-30 07:32:21+00', 'producttag');
INSERT INTO public.dtb_product_tag VALUES (242, 83, 2, NULL, '2022-06-30 07:32:21+00', 'producttag');
INSERT INTO public.dtb_product_tag VALUES (243, 83, 3, NULL, '2022-06-30 07:32:21+00', 'producttag');
INSERT INTO public.dtb_product_tag VALUES (244, 84, 1, NULL, '2022-06-30 07:32:22+00', 'producttag');
INSERT INTO public.dtb_product_tag VALUES (245, 84, 2, NULL, '2022-06-30 07:32:22+00', 'producttag');
INSERT INTO public.dtb_product_tag VALUES (246, 84, 3, NULL, '2022-06-30 07:32:22+00', 'producttag');
INSERT INTO public.dtb_product_tag VALUES (247, 85, 1, NULL, '2022-06-30 07:32:22+00', 'producttag');
INSERT INTO public.dtb_product_tag VALUES (248, 85, 2, NULL, '2022-06-30 07:32:22+00', 'producttag');
INSERT INTO public.dtb_product_tag VALUES (249, 85, 3, NULL, '2022-06-30 07:32:22+00', 'producttag');
INSERT INTO public.dtb_product_tag VALUES (250, 86, 1, NULL, '2022-06-30 07:32:23+00', 'producttag');
INSERT INTO public.dtb_product_tag VALUES (251, 86, 2, NULL, '2022-06-30 07:32:23+00', 'producttag');
INSERT INTO public.dtb_product_tag VALUES (252, 86, 3, NULL, '2022-06-30 07:32:23+00', 'producttag');
INSERT INTO public.dtb_product_tag VALUES (253, 87, 1, NULL, '2022-06-30 07:32:23+00', 'producttag');
INSERT INTO public.dtb_product_tag VALUES (254, 87, 2, NULL, '2022-06-30 07:32:23+00', 'producttag');
INSERT INTO public.dtb_product_tag VALUES (255, 87, 3, NULL, '2022-06-30 07:32:24+00', 'producttag');
INSERT INTO public.dtb_product_tag VALUES (256, 88, 1, NULL, '2022-06-30 07:32:24+00', 'producttag');
INSERT INTO public.dtb_product_tag VALUES (257, 88, 2, NULL, '2022-06-30 07:32:24+00', 'producttag');
INSERT INTO public.dtb_product_tag VALUES (258, 88, 3, NULL, '2022-06-30 07:32:24+00', 'producttag');
INSERT INTO public.dtb_product_tag VALUES (259, 89, 1, NULL, '2022-06-30 07:32:25+00', 'producttag');
INSERT INTO public.dtb_product_tag VALUES (260, 89, 2, NULL, '2022-06-30 07:32:25+00', 'producttag');
INSERT INTO public.dtb_product_tag VALUES (261, 89, 3, NULL, '2022-06-30 07:32:25+00', 'producttag');
INSERT INTO public.dtb_product_tag VALUES (262, 90, 1, NULL, '2022-06-30 07:32:25+00', 'producttag');
INSERT INTO public.dtb_product_tag VALUES (263, 90, 2, NULL, '2022-06-30 07:32:25+00', 'producttag');
INSERT INTO public.dtb_product_tag VALUES (264, 90, 3, NULL, '2022-06-30 07:32:25+00', 'producttag');
INSERT INTO public.dtb_product_tag VALUES (265, 91, 1, NULL, '2022-06-30 07:32:26+00', 'producttag');
INSERT INTO public.dtb_product_tag VALUES (266, 91, 2, NULL, '2022-06-30 07:32:26+00', 'producttag');
INSERT INTO public.dtb_product_tag VALUES (267, 91, 3, NULL, '2022-06-30 07:32:26+00', 'producttag');
INSERT INTO public.dtb_product_tag VALUES (268, 92, 1, NULL, '2022-06-30 07:32:26+00', 'producttag');
INSERT INTO public.dtb_product_tag VALUES (269, 92, 2, NULL, '2022-06-30 07:32:26+00', 'producttag');
INSERT INTO public.dtb_product_tag VALUES (270, 92, 3, NULL, '2022-06-30 07:32:26+00', 'producttag');
INSERT INTO public.dtb_product_tag VALUES (271, 93, 1, NULL, '2022-06-30 07:32:27+00', 'producttag');
INSERT INTO public.dtb_product_tag VALUES (272, 93, 2, NULL, '2022-06-30 07:32:27+00', 'producttag');
INSERT INTO public.dtb_product_tag VALUES (273, 93, 3, NULL, '2022-06-30 07:32:27+00', 'producttag');
INSERT INTO public.dtb_product_tag VALUES (274, 94, 1, NULL, '2022-06-30 07:32:28+00', 'producttag');
INSERT INTO public.dtb_product_tag VALUES (275, 94, 2, NULL, '2022-06-30 07:32:28+00', 'producttag');
INSERT INTO public.dtb_product_tag VALUES (276, 94, 3, NULL, '2022-06-30 07:32:28+00', 'producttag');
INSERT INTO public.dtb_product_tag VALUES (277, 95, 1, NULL, '2022-06-30 07:32:28+00', 'producttag');
INSERT INTO public.dtb_product_tag VALUES (278, 95, 2, NULL, '2022-06-30 07:32:28+00', 'producttag');
INSERT INTO public.dtb_product_tag VALUES (279, 95, 3, NULL, '2022-06-30 07:32:28+00', 'producttag');
INSERT INTO public.dtb_product_tag VALUES (280, 96, 1, NULL, '2022-06-30 07:32:29+00', 'producttag');
INSERT INTO public.dtb_product_tag VALUES (281, 96, 2, NULL, '2022-06-30 07:32:29+00', 'producttag');
INSERT INTO public.dtb_product_tag VALUES (282, 96, 3, NULL, '2022-06-30 07:32:29+00', 'producttag');
INSERT INTO public.dtb_product_tag VALUES (283, 97, 1, NULL, '2022-06-30 07:32:30+00', 'producttag');
INSERT INTO public.dtb_product_tag VALUES (284, 97, 2, NULL, '2022-06-30 07:32:30+00', 'producttag');
INSERT INTO public.dtb_product_tag VALUES (285, 97, 3, NULL, '2022-06-30 07:32:30+00', 'producttag');
INSERT INTO public.dtb_product_tag VALUES (286, 98, 1, NULL, '2022-06-30 07:32:30+00', 'producttag');
INSERT INTO public.dtb_product_tag VALUES (287, 98, 2, NULL, '2022-06-30 07:32:30+00', 'producttag');
INSERT INTO public.dtb_product_tag VALUES (288, 98, 3, NULL, '2022-06-30 07:32:30+00', 'producttag');
INSERT INTO public.dtb_product_tag VALUES (289, 99, 1, NULL, '2022-06-30 07:32:31+00', 'producttag');
INSERT INTO public.dtb_product_tag VALUES (290, 99, 2, NULL, '2022-06-30 07:32:31+00', 'producttag');
INSERT INTO public.dtb_product_tag VALUES (291, 99, 3, NULL, '2022-06-30 07:32:31+00', 'producttag');
INSERT INTO public.dtb_product_tag VALUES (292, 100, 1, NULL, '2022-06-30 07:32:31+00', 'producttag');
INSERT INTO public.dtb_product_tag VALUES (293, 100, 2, NULL, '2022-06-30 07:32:31+00', 'producttag');
INSERT INTO public.dtb_product_tag VALUES (294, 100, 3, NULL, '2022-06-30 07:32:31+00', 'producttag');
INSERT INTO public.dtb_product_tag VALUES (295, 101, 1, NULL, '2022-06-30 07:32:32+00', 'producttag');
INSERT INTO public.dtb_product_tag VALUES (296, 101, 2, NULL, '2022-06-30 07:32:32+00', 'producttag');
INSERT INTO public.dtb_product_tag VALUES (297, 101, 3, NULL, '2022-06-30 07:32:32+00', 'producttag');
INSERT INTO public.dtb_product_tag VALUES (298, 102, 1, NULL, '2022-06-30 07:32:33+00', 'producttag');
INSERT INTO public.dtb_product_tag VALUES (299, 102, 2, NULL, '2022-06-30 07:32:33+00', 'producttag');
INSERT INTO public.dtb_product_tag VALUES (300, 102, 3, NULL, '2022-06-30 07:32:33+00', 'producttag');


--
-- Data for Name: dtb_shipping; Type: TABLE DATA; Schema: public; Owner: dbuser
--

INSERT INTO public.dtb_shipping VALUES (1, 1, NULL, 7, 2, NULL, '吉本', '明美', 'ナギサ', 'リョウヘイ', '有限会社 渚', '09051823959', '8955032', '宮沢市', '井上町鈴木6-6-4', 'サンプル宅配', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2022-06-30 07:32:33+00', '2022-06-30 07:32:33+00', NULL, 'shipping');


--
-- Data for Name: dtb_tag; Type: TABLE DATA; Schema: public; Owner: dbuser
--

INSERT INTO public.dtb_tag VALUES (1, '新商品', 1, 'tag');
INSERT INTO public.dtb_tag VALUES (2, 'おすすめ商品', 2, 'tag');
INSERT INTO public.dtb_tag VALUES (3, '限定品', 3, 'tag');


--
-- Data for Name: dtb_tax_rule; Type: TABLE DATA; Schema: public; Owner: dbuser
--

INSERT INTO public.dtb_tax_rule VALUES (1, NULL, NULL, NULL, NULL, NULL, 1, 10, 0, '2017-03-07 10:14:52+00', '2017-03-07 10:14:52+00', '2017-03-07 10:14:52+00', 'taxrule');


--
-- Data for Name: dtb_template; Type: TABLE DATA; Schema: public; Owner: dbuser
--

INSERT INTO public.dtb_template VALUES (1, 10, 'default', 'デフォルト', '2017-03-07 10:14:52+00', '2017-03-07 10:14:52+00', 'template');


--
-- Data for Name: mtb_authority; Type: TABLE DATA; Schema: public; Owner: dbuser
--

INSERT INTO public.mtb_authority VALUES (0, 'システム管理者', 0, 'authority');
INSERT INTO public.mtb_authority VALUES (1, '店舗オーナー', 1, 'authority');


--
-- Data for Name: mtb_country; Type: TABLE DATA; Schema: public; Owner: dbuser
--

INSERT INTO public.mtb_country VALUES (352, 'アイスランド', 1, 'country');
INSERT INTO public.mtb_country VALUES (372, 'アイルランド', 2, 'country');
INSERT INTO public.mtb_country VALUES (31, 'アゼルバイジャン', 3, 'country');
INSERT INTO public.mtb_country VALUES (4, 'アフガニスタン', 4, 'country');
INSERT INTO public.mtb_country VALUES (840, 'アメリカ合衆国', 5, 'country');
INSERT INTO public.mtb_country VALUES (850, 'アメリカ領ヴァージン諸島', 6, 'country');
INSERT INTO public.mtb_country VALUES (16, 'アメリカ領サモア', 7, 'country');
INSERT INTO public.mtb_country VALUES (784, 'アラブ首長国連邦', 8, 'country');
INSERT INTO public.mtb_country VALUES (12, 'アルジェリア', 9, 'country');
INSERT INTO public.mtb_country VALUES (32, 'アルゼンチン', 10, 'country');
INSERT INTO public.mtb_country VALUES (533, 'アルバ', 11, 'country');
INSERT INTO public.mtb_country VALUES (8, 'アルバニア', 12, 'country');
INSERT INTO public.mtb_country VALUES (51, 'アルメニア', 13, 'country');
INSERT INTO public.mtb_country VALUES (660, 'アンギラ', 14, 'country');
INSERT INTO public.mtb_country VALUES (24, 'アンゴラ', 15, 'country');
INSERT INTO public.mtb_country VALUES (28, 'アンティグア・バーブーダ', 16, 'country');
INSERT INTO public.mtb_country VALUES (20, 'アンドラ', 17, 'country');
INSERT INTO public.mtb_country VALUES (887, 'イエメン', 18, 'country');
INSERT INTO public.mtb_country VALUES (826, 'イギリス', 19, 'country');
INSERT INTO public.mtb_country VALUES (86, 'イギリス領インド洋地域', 20, 'country');
INSERT INTO public.mtb_country VALUES (92, 'イギリス領ヴァージン諸島', 21, 'country');
INSERT INTO public.mtb_country VALUES (376, 'イスラエル', 22, 'country');
INSERT INTO public.mtb_country VALUES (380, 'イタリア', 23, 'country');
INSERT INTO public.mtb_country VALUES (368, 'イラク', 24, 'country');
INSERT INTO public.mtb_country VALUES (364, 'イラン|イラン・イスラム共和国', 25, 'country');
INSERT INTO public.mtb_country VALUES (356, 'インド', 26, 'country');
INSERT INTO public.mtb_country VALUES (360, 'インドネシア', 27, 'country');
INSERT INTO public.mtb_country VALUES (876, 'ウォリス・フツナ', 28, 'country');
INSERT INTO public.mtb_country VALUES (800, 'ウガンダ', 29, 'country');
INSERT INTO public.mtb_country VALUES (804, 'ウクライナ', 30, 'country');
INSERT INTO public.mtb_country VALUES (860, 'ウズベキスタン', 31, 'country');
INSERT INTO public.mtb_country VALUES (858, 'ウルグアイ', 32, 'country');
INSERT INTO public.mtb_country VALUES (218, 'エクアドル', 33, 'country');
INSERT INTO public.mtb_country VALUES (818, 'エジプト', 34, 'country');
INSERT INTO public.mtb_country VALUES (233, 'エストニア', 35, 'country');
INSERT INTO public.mtb_country VALUES (231, 'エチオピア', 36, 'country');
INSERT INTO public.mtb_country VALUES (232, 'エリトリア', 37, 'country');
INSERT INTO public.mtb_country VALUES (222, 'エルサルバドル', 38, 'country');
INSERT INTO public.mtb_country VALUES (36, 'オーストラリア', 39, 'country');
INSERT INTO public.mtb_country VALUES (40, 'オーストリア', 40, 'country');
INSERT INTO public.mtb_country VALUES (248, 'オーランド諸島', 41, 'country');
INSERT INTO public.mtb_country VALUES (512, 'オマーン', 42, 'country');
INSERT INTO public.mtb_country VALUES (528, 'オランダ', 43, 'country');
INSERT INTO public.mtb_country VALUES (288, 'ガーナ', 44, 'country');
INSERT INTO public.mtb_country VALUES (132, 'カーボベルデ', 45, 'country');
INSERT INTO public.mtb_country VALUES (831, 'ガーンジー', 46, 'country');
INSERT INTO public.mtb_country VALUES (328, 'ガイアナ', 47, 'country');
INSERT INTO public.mtb_country VALUES (398, 'カザフスタン', 48, 'country');
INSERT INTO public.mtb_country VALUES (634, 'カタール', 49, 'country');
INSERT INTO public.mtb_country VALUES (581, '合衆国領有小離島', 50, 'country');
INSERT INTO public.mtb_country VALUES (124, 'カナダ', 51, 'country');
INSERT INTO public.mtb_country VALUES (266, 'ガボン', 52, 'country');
INSERT INTO public.mtb_country VALUES (120, 'カメルーン', 53, 'country');
INSERT INTO public.mtb_country VALUES (270, 'ガンビア', 54, 'country');
INSERT INTO public.mtb_country VALUES (116, 'カンボジア', 55, 'country');
INSERT INTO public.mtb_country VALUES (580, '北マリアナ諸島', 56, 'country');
INSERT INTO public.mtb_country VALUES (324, 'ギニア', 57, 'country');
INSERT INTO public.mtb_country VALUES (624, 'ギニアビサウ', 58, 'country');
INSERT INTO public.mtb_country VALUES (196, 'キプロス', 59, 'country');
INSERT INTO public.mtb_country VALUES (192, 'キューバ', 60, 'country');
INSERT INTO public.mtb_country VALUES (531, 'キュラソー島|キュラソー', 61, 'country');
INSERT INTO public.mtb_country VALUES (300, 'ギリシャ', 62, 'country');
INSERT INTO public.mtb_country VALUES (296, 'キリバス', 63, 'country');
INSERT INTO public.mtb_country VALUES (417, 'キルギス', 64, 'country');
INSERT INTO public.mtb_country VALUES (320, 'グアテマラ', 65, 'country');
INSERT INTO public.mtb_country VALUES (312, 'グアドループ', 66, 'country');
INSERT INTO public.mtb_country VALUES (316, 'グアム', 67, 'country');
INSERT INTO public.mtb_country VALUES (414, 'クウェート', 68, 'country');
INSERT INTO public.mtb_country VALUES (184, 'クック諸島', 69, 'country');
INSERT INTO public.mtb_country VALUES (304, 'グリーンランド', 70, 'country');
INSERT INTO public.mtb_country VALUES (162, 'クリスマス島 (オーストラリア)|クリスマス島', 71, 'country');
INSERT INTO public.mtb_country VALUES (268, 'グルジア', 72, 'country');
INSERT INTO public.mtb_country VALUES (308, 'グレナダ', 73, 'country');
INSERT INTO public.mtb_country VALUES (191, 'クロアチア', 74, 'country');
INSERT INTO public.mtb_country VALUES (136, 'ケイマン諸島', 75, 'country');
INSERT INTO public.mtb_country VALUES (404, 'ケニア', 76, 'country');
INSERT INTO public.mtb_country VALUES (384, 'コートジボワール', 77, 'country');
INSERT INTO public.mtb_country VALUES (166, 'ココス諸島|ココス（キーリング）諸島', 78, 'country');
INSERT INTO public.mtb_country VALUES (188, 'コスタリカ', 79, 'country');
INSERT INTO public.mtb_country VALUES (174, 'コモロ', 80, 'country');
INSERT INTO public.mtb_country VALUES (170, 'コロンビア', 81, 'country');
INSERT INTO public.mtb_country VALUES (178, 'コンゴ共和国', 82, 'country');
INSERT INTO public.mtb_country VALUES (180, 'コンゴ民主共和国', 83, 'country');
INSERT INTO public.mtb_country VALUES (682, 'サウジアラビア', 84, 'country');
INSERT INTO public.mtb_country VALUES (239, 'サウスジョージア・サウスサンドウィッチ諸島', 85, 'country');
INSERT INTO public.mtb_country VALUES (882, 'サモア', 86, 'country');
INSERT INTO public.mtb_country VALUES (678, 'サントメ・プリンシペ', 87, 'country');
INSERT INTO public.mtb_country VALUES (652, 'サン・バルテルミー島|サン・バルテルミー', 88, 'country');
INSERT INTO public.mtb_country VALUES (894, 'ザンビア', 89, 'country');
INSERT INTO public.mtb_country VALUES (666, 'サンピエール島・ミクロン島', 90, 'country');
INSERT INTO public.mtb_country VALUES (674, 'サンマリノ', 91, 'country');
INSERT INTO public.mtb_country VALUES (663, 'サン・マルタン (西インド諸島)|サン・マルタン（フランス領）', 92, 'country');
INSERT INTO public.mtb_country VALUES (694, 'シエラレオネ', 93, 'country');
INSERT INTO public.mtb_country VALUES (262, 'ジブチ', 94, 'country');
INSERT INTO public.mtb_country VALUES (292, 'ジブラルタル', 95, 'country');
INSERT INTO public.mtb_country VALUES (832, 'ジャージー', 96, 'country');
INSERT INTO public.mtb_country VALUES (388, 'ジャマイカ', 97, 'country');
INSERT INTO public.mtb_country VALUES (760, 'シリア|シリア・アラブ共和国', 98, 'country');
INSERT INTO public.mtb_country VALUES (702, 'シンガポール', 99, 'country');
INSERT INTO public.mtb_country VALUES (534, 'シント・マールテン|シント・マールテン（オランダ領）', 100, 'country');
INSERT INTO public.mtb_country VALUES (716, 'ジンバブエ', 101, 'country');
INSERT INTO public.mtb_country VALUES (756, 'スイス', 102, 'country');
INSERT INTO public.mtb_country VALUES (752, 'スウェーデン', 103, 'country');
INSERT INTO public.mtb_country VALUES (729, 'スーダン', 104, 'country');
INSERT INTO public.mtb_country VALUES (744, 'スヴァールバル諸島およびヤンマイエン島', 105, 'country');
INSERT INTO public.mtb_country VALUES (724, 'スペイン', 106, 'country');
INSERT INTO public.mtb_country VALUES (740, 'スリナム', 107, 'country');
INSERT INTO public.mtb_country VALUES (144, 'スリランカ', 108, 'country');
INSERT INTO public.mtb_country VALUES (703, 'スロバキア', 109, 'country');
INSERT INTO public.mtb_country VALUES (705, 'スロベニア', 110, 'country');
INSERT INTO public.mtb_country VALUES (748, 'スワジランド', 111, 'country');
INSERT INTO public.mtb_country VALUES (690, 'セーシェル', 112, 'country');
INSERT INTO public.mtb_country VALUES (226, '赤道ギニア', 113, 'country');
INSERT INTO public.mtb_country VALUES (686, 'セネガル', 114, 'country');
INSERT INTO public.mtb_country VALUES (688, 'セルビア', 115, 'country');
INSERT INTO public.mtb_country VALUES (659, 'セントクリストファー・ネイビス', 116, 'country');
INSERT INTO public.mtb_country VALUES (670, 'セントビンセント・グレナディーン|セントビンセントおよびグレナディーン諸島', 117, 'country');
INSERT INTO public.mtb_country VALUES (426, 'レソト', 246, 'country');
INSERT INTO public.mtb_country VALUES (654, 'セントヘレナ・アセンションおよびトリスタンダクーニャ', 118, 'country');
INSERT INTO public.mtb_country VALUES (662, 'セントルシア', 119, 'country');
INSERT INTO public.mtb_country VALUES (706, 'ソマリア', 120, 'country');
INSERT INTO public.mtb_country VALUES (90, 'ソロモン諸島', 121, 'country');
INSERT INTO public.mtb_country VALUES (796, 'タークス・カイコス諸島', 122, 'country');
INSERT INTO public.mtb_country VALUES (764, 'タイ王国|タイ', 123, 'country');
INSERT INTO public.mtb_country VALUES (410, '大韓民国', 124, 'country');
INSERT INTO public.mtb_country VALUES (158, '台湾', 125, 'country');
INSERT INTO public.mtb_country VALUES (762, 'タジキスタン', 126, 'country');
INSERT INTO public.mtb_country VALUES (834, 'タンザニア', 127, 'country');
INSERT INTO public.mtb_country VALUES (203, 'チェコ', 128, 'country');
INSERT INTO public.mtb_country VALUES (148, 'チャド', 129, 'country');
INSERT INTO public.mtb_country VALUES (140, '中央アフリカ共和国', 130, 'country');
INSERT INTO public.mtb_country VALUES (156, '中華人民共和国|中国', 131, 'country');
INSERT INTO public.mtb_country VALUES (788, 'チュニジア', 132, 'country');
INSERT INTO public.mtb_country VALUES (408, '朝鮮民主主義人民共和国', 133, 'country');
INSERT INTO public.mtb_country VALUES (152, 'チリ', 134, 'country');
INSERT INTO public.mtb_country VALUES (798, 'ツバル', 135, 'country');
INSERT INTO public.mtb_country VALUES (208, 'デンマーク', 136, 'country');
INSERT INTO public.mtb_country VALUES (276, 'ドイツ', 137, 'country');
INSERT INTO public.mtb_country VALUES (768, 'トーゴ', 138, 'country');
INSERT INTO public.mtb_country VALUES (772, 'トケラウ', 139, 'country');
INSERT INTO public.mtb_country VALUES (214, 'ドミニカ共和国', 140, 'country');
INSERT INTO public.mtb_country VALUES (212, 'ドミニカ国', 141, 'country');
INSERT INTO public.mtb_country VALUES (780, 'トリニダード・トバゴ', 142, 'country');
INSERT INTO public.mtb_country VALUES (795, 'トルクメニスタン', 143, 'country');
INSERT INTO public.mtb_country VALUES (792, 'トルコ', 144, 'country');
INSERT INTO public.mtb_country VALUES (776, 'トンガ', 145, 'country');
INSERT INTO public.mtb_country VALUES (566, 'ナイジェリア', 146, 'country');
INSERT INTO public.mtb_country VALUES (520, 'ナウル', 147, 'country');
INSERT INTO public.mtb_country VALUES (516, 'ナミビア', 148, 'country');
INSERT INTO public.mtb_country VALUES (10, '南極', 149, 'country');
INSERT INTO public.mtb_country VALUES (570, 'ニウエ', 150, 'country');
INSERT INTO public.mtb_country VALUES (558, 'ニカラグア', 151, 'country');
INSERT INTO public.mtb_country VALUES (562, 'ニジェール', 152, 'country');
INSERT INTO public.mtb_country VALUES (392, '日本', 153, 'country');
INSERT INTO public.mtb_country VALUES (732, '西サハラ', 154, 'country');
INSERT INTO public.mtb_country VALUES (540, 'ニューカレドニア', 155, 'country');
INSERT INTO public.mtb_country VALUES (554, 'ニュージーランド', 156, 'country');
INSERT INTO public.mtb_country VALUES (524, 'ネパール', 157, 'country');
INSERT INTO public.mtb_country VALUES (574, 'ノーフォーク島', 158, 'country');
INSERT INTO public.mtb_country VALUES (578, 'ノルウェー', 159, 'country');
INSERT INTO public.mtb_country VALUES (334, 'ハード島とマクドナルド諸島', 160, 'country');
INSERT INTO public.mtb_country VALUES (48, 'バーレーン', 161, 'country');
INSERT INTO public.mtb_country VALUES (332, 'ハイチ', 162, 'country');
INSERT INTO public.mtb_country VALUES (586, 'パキスタン', 163, 'country');
INSERT INTO public.mtb_country VALUES (336, 'バチカン|バチカン市国', 164, 'country');
INSERT INTO public.mtb_country VALUES (591, 'パナマ', 165, 'country');
INSERT INTO public.mtb_country VALUES (548, 'バヌアツ', 166, 'country');
INSERT INTO public.mtb_country VALUES (44, 'バハマ', 167, 'country');
INSERT INTO public.mtb_country VALUES (598, 'パプアニューギニア', 168, 'country');
INSERT INTO public.mtb_country VALUES (60, 'バミューダ諸島|バミューダ', 169, 'country');
INSERT INTO public.mtb_country VALUES (585, 'パラオ', 170, 'country');
INSERT INTO public.mtb_country VALUES (600, 'パラグアイ', 171, 'country');
INSERT INTO public.mtb_country VALUES (52, 'バルバドス', 172, 'country');
INSERT INTO public.mtb_country VALUES (275, 'パレスチナ', 173, 'country');
INSERT INTO public.mtb_country VALUES (348, 'ハンガリー', 174, 'country');
INSERT INTO public.mtb_country VALUES (50, 'バングラデシュ', 175, 'country');
INSERT INTO public.mtb_country VALUES (626, '東ティモール', 176, 'country');
INSERT INTO public.mtb_country VALUES (612, 'ピトケアン諸島|ピトケアン', 177, 'country');
INSERT INTO public.mtb_country VALUES (242, 'フィジー', 178, 'country');
INSERT INTO public.mtb_country VALUES (608, 'フィリピン', 179, 'country');
INSERT INTO public.mtb_country VALUES (246, 'フィンランド', 180, 'country');
INSERT INTO public.mtb_country VALUES (64, 'ブータン', 181, 'country');
INSERT INTO public.mtb_country VALUES (74, 'ブーベ島', 182, 'country');
INSERT INTO public.mtb_country VALUES (630, 'プエルトリコ', 183, 'country');
INSERT INTO public.mtb_country VALUES (234, 'フェロー諸島', 184, 'country');
INSERT INTO public.mtb_country VALUES (238, 'フォークランド諸島|フォークランド（マルビナス）諸島', 185, 'country');
INSERT INTO public.mtb_country VALUES (76, 'ブラジル', 186, 'country');
INSERT INTO public.mtb_country VALUES (250, 'フランス', 187, 'country');
INSERT INTO public.mtb_country VALUES (254, 'フランス領ギアナ', 188, 'country');
INSERT INTO public.mtb_country VALUES (258, 'フランス領ポリネシア', 189, 'country');
INSERT INTO public.mtb_country VALUES (260, 'フランス領南方・南極地域', 190, 'country');
INSERT INTO public.mtb_country VALUES (100, 'ブルガリア', 191, 'country');
INSERT INTO public.mtb_country VALUES (854, 'ブルキナファソ', 192, 'country');
INSERT INTO public.mtb_country VALUES (96, 'ブルネイ|ブルネイ・ダルサラーム', 193, 'country');
INSERT INTO public.mtb_country VALUES (108, 'ブルンジ', 194, 'country');
INSERT INTO public.mtb_country VALUES (704, 'ベトナム', 195, 'country');
INSERT INTO public.mtb_country VALUES (204, 'ベナン', 196, 'country');
INSERT INTO public.mtb_country VALUES (862, 'ベネズエラ|ベネズエラ・ボリバル共和国', 197, 'country');
INSERT INTO public.mtb_country VALUES (112, 'ベラルーシ', 198, 'country');
INSERT INTO public.mtb_country VALUES (84, 'ベリーズ', 199, 'country');
INSERT INTO public.mtb_country VALUES (604, 'ペルー', 200, 'country');
INSERT INTO public.mtb_country VALUES (56, 'ベルギー', 201, 'country');
INSERT INTO public.mtb_country VALUES (616, 'ポーランド', 202, 'country');
INSERT INTO public.mtb_country VALUES (70, 'ボスニア・ヘルツェゴビナ', 203, 'country');
INSERT INTO public.mtb_country VALUES (72, 'ボツワナ', 204, 'country');
INSERT INTO public.mtb_country VALUES (535, 'BES諸島|ボネール、シント・ユースタティウスおよびサバ', 205, 'country');
INSERT INTO public.mtb_country VALUES (68, 'ボリビア|ボリビア多民族国', 206, 'country');
INSERT INTO public.mtb_country VALUES (620, 'ポルトガル', 207, 'country');
INSERT INTO public.mtb_country VALUES (344, '香港', 208, 'country');
INSERT INTO public.mtb_country VALUES (340, 'ホンジュラス', 209, 'country');
INSERT INTO public.mtb_country VALUES (584, 'マーシャル諸島', 210, 'country');
INSERT INTO public.mtb_country VALUES (446, 'マカオ', 211, 'country');
INSERT INTO public.mtb_country VALUES (807, 'マケドニア共和国|マケドニア旧ユーゴスラビア共和国', 212, 'country');
INSERT INTO public.mtb_country VALUES (450, 'マダガスカル', 213, 'country');
INSERT INTO public.mtb_country VALUES (175, 'マヨット', 214, 'country');
INSERT INTO public.mtb_country VALUES (454, 'マラウイ', 215, 'country');
INSERT INTO public.mtb_country VALUES (466, 'マリ共和国|マリ', 216, 'country');
INSERT INTO public.mtb_country VALUES (470, 'マルタ', 217, 'country');
INSERT INTO public.mtb_country VALUES (474, 'マルティニーク', 218, 'country');
INSERT INTO public.mtb_country VALUES (458, 'マレーシア', 219, 'country');
INSERT INTO public.mtb_country VALUES (833, 'マン島', 220, 'country');
INSERT INTO public.mtb_country VALUES (583, 'ミクロネシア連邦', 221, 'country');
INSERT INTO public.mtb_country VALUES (710, '南アフリカ共和国|南アフリカ', 222, 'country');
INSERT INTO public.mtb_country VALUES (728, '南スーダン', 223, 'country');
INSERT INTO public.mtb_country VALUES (104, 'ミャンマー', 224, 'country');
INSERT INTO public.mtb_country VALUES (484, 'メキシコ', 225, 'country');
INSERT INTO public.mtb_country VALUES (480, 'モーリシャス', 226, 'country');
INSERT INTO public.mtb_country VALUES (478, 'モーリタニア', 227, 'country');
INSERT INTO public.mtb_country VALUES (508, 'モザンビーク', 228, 'country');
INSERT INTO public.mtb_country VALUES (492, 'モナコ', 229, 'country');
INSERT INTO public.mtb_country VALUES (462, 'モルディブ', 230, 'country');
INSERT INTO public.mtb_country VALUES (498, 'モルドバ|モルドバ共和国', 231, 'country');
INSERT INTO public.mtb_country VALUES (504, 'モロッコ', 232, 'country');
INSERT INTO public.mtb_country VALUES (496, 'モンゴル国|モンゴル', 233, 'country');
INSERT INTO public.mtb_country VALUES (499, 'モンテネグロ', 234, 'country');
INSERT INTO public.mtb_country VALUES (500, 'モントセラト', 235, 'country');
INSERT INTO public.mtb_country VALUES (400, 'ヨルダン', 236, 'country');
INSERT INTO public.mtb_country VALUES (418, 'ラオス|ラオス人民民主共和国', 237, 'country');
INSERT INTO public.mtb_country VALUES (428, 'ラトビア', 238, 'country');
INSERT INTO public.mtb_country VALUES (440, 'リトアニア', 239, 'country');
INSERT INTO public.mtb_country VALUES (434, 'リビア', 240, 'country');
INSERT INTO public.mtb_country VALUES (438, 'リヒテンシュタイン', 241, 'country');
INSERT INTO public.mtb_country VALUES (430, 'リベリア', 242, 'country');
INSERT INTO public.mtb_country VALUES (642, 'ルーマニア', 243, 'country');
INSERT INTO public.mtb_country VALUES (442, 'ルクセンブルク', 244, 'country');
INSERT INTO public.mtb_country VALUES (646, 'ルワンダ', 245, 'country');
INSERT INTO public.mtb_country VALUES (422, 'レバノン', 247, 'country');
INSERT INTO public.mtb_country VALUES (638, 'レユニオン', 248, 'country');
INSERT INTO public.mtb_country VALUES (643, 'ロシア|ロシア連邦', 249, 'country');


--
-- Data for Name: mtb_csv_type; Type: TABLE DATA; Schema: public; Owner: dbuser
--

INSERT INTO public.mtb_csv_type VALUES (1, '商品CSV', 3, 'csvtype');
INSERT INTO public.mtb_csv_type VALUES (2, '会員CSV', 4, 'csvtype');
INSERT INTO public.mtb_csv_type VALUES (3, '受注CSV', 1, 'csvtype');
INSERT INTO public.mtb_csv_type VALUES (4, '配送CSV', 1, 'csvtype');
INSERT INTO public.mtb_csv_type VALUES (5, 'カテゴリCSV', 5, 'csvtype');


--
-- Data for Name: mtb_customer_order_status; Type: TABLE DATA; Schema: public; Owner: dbuser
--

INSERT INTO public.mtb_customer_order_status VALUES (1, '注文受付', 0, 'customerorderstatus');
INSERT INTO public.mtb_customer_order_status VALUES (3, '注文取消し', 4, 'customerorderstatus');
INSERT INTO public.mtb_customer_order_status VALUES (4, '注文受付', 3, 'customerorderstatus');
INSERT INTO public.mtb_customer_order_status VALUES (5, '発送済み', 6, 'customerorderstatus');
INSERT INTO public.mtb_customer_order_status VALUES (6, '注文受付', 2, 'customerorderstatus');
INSERT INTO public.mtb_customer_order_status VALUES (7, '注文受付', 1, 'customerorderstatus');
INSERT INTO public.mtb_customer_order_status VALUES (8, '注文未完了', 5, 'customerorderstatus');
INSERT INTO public.mtb_customer_order_status VALUES (9, '返品', 7, 'customerorderstatus');


--
-- Data for Name: mtb_customer_status; Type: TABLE DATA; Schema: public; Owner: dbuser
--

INSERT INTO public.mtb_customer_status VALUES (1, '仮会員', 0, 'customerstatus');
INSERT INTO public.mtb_customer_status VALUES (2, '本会員', 1, 'customerstatus');
INSERT INTO public.mtb_customer_status VALUES (3, '退会', 2, 'customerstatus');


--
-- Data for Name: mtb_device_type; Type: TABLE DATA; Schema: public; Owner: dbuser
--

INSERT INTO public.mtb_device_type VALUES (2, 'モバイル', 0, 'devicetype');
INSERT INTO public.mtb_device_type VALUES (10, 'PC', 1, 'devicetype');


--
-- Data for Name: mtb_job; Type: TABLE DATA; Schema: public; Owner: dbuser
--

INSERT INTO public.mtb_job VALUES (1, '公務員', 0, 'job');
INSERT INTO public.mtb_job VALUES (2, 'コンサルタント', 1, 'job');
INSERT INTO public.mtb_job VALUES (3, 'コンピューター関連技術職', 2, 'job');
INSERT INTO public.mtb_job VALUES (4, 'コンピューター関連以外の技術職', 3, 'job');
INSERT INTO public.mtb_job VALUES (5, '金融関係', 4, 'job');
INSERT INTO public.mtb_job VALUES (6, '医師', 5, 'job');
INSERT INTO public.mtb_job VALUES (7, '弁護士', 6, 'job');
INSERT INTO public.mtb_job VALUES (8, '総務・人事・事務', 7, 'job');
INSERT INTO public.mtb_job VALUES (9, '営業・販売', 8, 'job');
INSERT INTO public.mtb_job VALUES (10, '研究・開発', 9, 'job');
INSERT INTO public.mtb_job VALUES (11, '広報・宣伝', 10, 'job');
INSERT INTO public.mtb_job VALUES (12, '企画・マーケティング', 11, 'job');
INSERT INTO public.mtb_job VALUES (13, 'デザイン関係', 12, 'job');
INSERT INTO public.mtb_job VALUES (14, '会社経営・役員', 13, 'job');
INSERT INTO public.mtb_job VALUES (15, '出版・マスコミ関係', 14, 'job');
INSERT INTO public.mtb_job VALUES (16, '学生・フリーター', 15, 'job');
INSERT INTO public.mtb_job VALUES (17, '主婦', 16, 'job');
INSERT INTO public.mtb_job VALUES (18, 'その他', 17, 'job');


--
-- Data for Name: mtb_login_history_status; Type: TABLE DATA; Schema: public; Owner: dbuser
--

INSERT INTO public.mtb_login_history_status VALUES (0, '失敗', 0, 'loginhistorystatus');
INSERT INTO public.mtb_login_history_status VALUES (1, '成功', 1, 'loginhistorystatus');


--
-- Data for Name: mtb_order_item_type; Type: TABLE DATA; Schema: public; Owner: dbuser
--

INSERT INTO public.mtb_order_item_type VALUES (1, '商品', 0, 'orderitemtype');
INSERT INTO public.mtb_order_item_type VALUES (2, '送料', 1, 'orderitemtype');
INSERT INTO public.mtb_order_item_type VALUES (3, '手数料', 2, 'orderitemtype');
INSERT INTO public.mtb_order_item_type VALUES (4, '割引', 3, 'orderitemtype');
INSERT INTO public.mtb_order_item_type VALUES (5, '税', 4, 'orderitemtype');
INSERT INTO public.mtb_order_item_type VALUES (6, 'ポイント', 5, 'orderitemtype');


--
-- Data for Name: mtb_order_status; Type: TABLE DATA; Schema: public; Owner: dbuser
--

INSERT INTO public.mtb_order_status VALUES (1, true, '新規受付', 0, 'orderstatus');
INSERT INTO public.mtb_order_status VALUES (3, false, '注文取消し', 3, 'orderstatus');
INSERT INTO public.mtb_order_status VALUES (4, true, '対応中', 2, 'orderstatus');
INSERT INTO public.mtb_order_status VALUES (5, false, '発送済み', 4, 'orderstatus');
INSERT INTO public.mtb_order_status VALUES (6, true, '入金済み', 1, 'orderstatus');
INSERT INTO public.mtb_order_status VALUES (7, false, '決済処理中', 6, 'orderstatus');
INSERT INTO public.mtb_order_status VALUES (8, false, '購入処理中', 5, 'orderstatus');
INSERT INTO public.mtb_order_status VALUES (9, false, '返品', 7, 'orderstatus');


--
-- Data for Name: mtb_order_status_color; Type: TABLE DATA; Schema: public; Owner: dbuser
--

INSERT INTO public.mtb_order_status_color VALUES (1, '#437ec4', 0, 'orderstatuscolor');
INSERT INTO public.mtb_order_status_color VALUES (3, '#C04949', 3, 'orderstatuscolor');
INSERT INTO public.mtb_order_status_color VALUES (4, '#EEB128', 2, 'orderstatuscolor');
INSERT INTO public.mtb_order_status_color VALUES (5, '#25B877', 4, 'orderstatuscolor');
INSERT INTO public.mtb_order_status_color VALUES (6, '#25B877', 1, 'orderstatuscolor');
INSERT INTO public.mtb_order_status_color VALUES (7, '#A3A3A3', 6, 'orderstatuscolor');
INSERT INTO public.mtb_order_status_color VALUES (8, '#A3A3A3', 5, 'orderstatuscolor');
INSERT INTO public.mtb_order_status_color VALUES (9, '#C04949', 7, 'orderstatuscolor');


--
-- Data for Name: mtb_page_max; Type: TABLE DATA; Schema: public; Owner: dbuser
--

INSERT INTO public.mtb_page_max VALUES (10, '10', 0, 'pagemax');
INSERT INTO public.mtb_page_max VALUES (20, '20', 1, 'pagemax');
INSERT INTO public.mtb_page_max VALUES (30, '30', 2, 'pagemax');
INSERT INTO public.mtb_page_max VALUES (40, '40', 3, 'pagemax');
INSERT INTO public.mtb_page_max VALUES (50, '50', 4, 'pagemax');
INSERT INTO public.mtb_page_max VALUES (60, '60', 5, 'pagemax');
INSERT INTO public.mtb_page_max VALUES (70, '70', 6, 'pagemax');
INSERT INTO public.mtb_page_max VALUES (80, '80', 7, 'pagemax');
INSERT INTO public.mtb_page_max VALUES (90, '90', 8, 'pagemax');
INSERT INTO public.mtb_page_max VALUES (100, '100', 9, 'pagemax');


--
-- Data for Name: mtb_pref; Type: TABLE DATA; Schema: public; Owner: dbuser
--

INSERT INTO public.mtb_pref VALUES (1, '北海道', 1, 'pref');
INSERT INTO public.mtb_pref VALUES (2, '青森県', 2, 'pref');
INSERT INTO public.mtb_pref VALUES (3, '岩手県', 3, 'pref');
INSERT INTO public.mtb_pref VALUES (4, '宮城県', 4, 'pref');
INSERT INTO public.mtb_pref VALUES (5, '秋田県', 5, 'pref');
INSERT INTO public.mtb_pref VALUES (6, '山形県', 6, 'pref');
INSERT INTO public.mtb_pref VALUES (7, '福島県', 7, 'pref');
INSERT INTO public.mtb_pref VALUES (8, '茨城県', 8, 'pref');
INSERT INTO public.mtb_pref VALUES (9, '栃木県', 9, 'pref');
INSERT INTO public.mtb_pref VALUES (10, '群馬県', 10, 'pref');
INSERT INTO public.mtb_pref VALUES (11, '埼玉県', 11, 'pref');
INSERT INTO public.mtb_pref VALUES (12, '千葉県', 12, 'pref');
INSERT INTO public.mtb_pref VALUES (13, '東京都', 13, 'pref');
INSERT INTO public.mtb_pref VALUES (14, '神奈川県', 14, 'pref');
INSERT INTO public.mtb_pref VALUES (15, '新潟県', 15, 'pref');
INSERT INTO public.mtb_pref VALUES (16, '富山県', 16, 'pref');
INSERT INTO public.mtb_pref VALUES (17, '石川県', 17, 'pref');
INSERT INTO public.mtb_pref VALUES (18, '福井県', 18, 'pref');
INSERT INTO public.mtb_pref VALUES (19, '山梨県', 19, 'pref');
INSERT INTO public.mtb_pref VALUES (20, '長野県', 20, 'pref');
INSERT INTO public.mtb_pref VALUES (21, '岐阜県', 21, 'pref');
INSERT INTO public.mtb_pref VALUES (22, '静岡県', 22, 'pref');
INSERT INTO public.mtb_pref VALUES (23, '愛知県', 23, 'pref');
INSERT INTO public.mtb_pref VALUES (24, '三重県', 24, 'pref');
INSERT INTO public.mtb_pref VALUES (25, '滋賀県', 25, 'pref');
INSERT INTO public.mtb_pref VALUES (26, '京都府', 26, 'pref');
INSERT INTO public.mtb_pref VALUES (27, '大阪府', 27, 'pref');
INSERT INTO public.mtb_pref VALUES (28, '兵庫県', 28, 'pref');
INSERT INTO public.mtb_pref VALUES (29, '奈良県', 29, 'pref');
INSERT INTO public.mtb_pref VALUES (30, '和歌山県', 30, 'pref');
INSERT INTO public.mtb_pref VALUES (31, '鳥取県', 31, 'pref');
INSERT INTO public.mtb_pref VALUES (32, '島根県', 32, 'pref');
INSERT INTO public.mtb_pref VALUES (33, '岡山県', 33, 'pref');
INSERT INTO public.mtb_pref VALUES (34, '広島県', 34, 'pref');
INSERT INTO public.mtb_pref VALUES (35, '山口県', 35, 'pref');
INSERT INTO public.mtb_pref VALUES (36, '徳島県', 36, 'pref');
INSERT INTO public.mtb_pref VALUES (37, '香川県', 37, 'pref');
INSERT INTO public.mtb_pref VALUES (38, '愛媛県', 38, 'pref');
INSERT INTO public.mtb_pref VALUES (39, '高知県', 39, 'pref');
INSERT INTO public.mtb_pref VALUES (40, '福岡県', 40, 'pref');
INSERT INTO public.mtb_pref VALUES (41, '佐賀県', 41, 'pref');
INSERT INTO public.mtb_pref VALUES (42, '長崎県', 42, 'pref');
INSERT INTO public.mtb_pref VALUES (43, '熊本県', 43, 'pref');
INSERT INTO public.mtb_pref VALUES (44, '大分県', 44, 'pref');
INSERT INTO public.mtb_pref VALUES (45, '宮崎県', 45, 'pref');
INSERT INTO public.mtb_pref VALUES (46, '鹿児島県', 46, 'pref');
INSERT INTO public.mtb_pref VALUES (47, '沖縄県', 47, 'pref');


--
-- Data for Name: mtb_product_list_max; Type: TABLE DATA; Schema: public; Owner: dbuser
--

INSERT INTO public.mtb_product_list_max VALUES (20, '20件', 0, 'productlistmax');
INSERT INTO public.mtb_product_list_max VALUES (40, '40件', 1, 'productlistmax');
INSERT INTO public.mtb_product_list_max VALUES (60, '60件', 2, 'productlistmax');


--
-- Data for Name: mtb_product_list_order_by; Type: TABLE DATA; Schema: public; Owner: dbuser
--

INSERT INTO public.mtb_product_list_order_by VALUES (1, '価格が低い順', 0, 'productlistorderby');
INSERT INTO public.mtb_product_list_order_by VALUES (2, '新着順', 2, 'productlistorderby');
INSERT INTO public.mtb_product_list_order_by VALUES (3, '価格が高い順', 1, 'productlistorderby');


--
-- Data for Name: mtb_product_status; Type: TABLE DATA; Schema: public; Owner: dbuser
--

INSERT INTO public.mtb_product_status VALUES (1, '公開', 0, 'productstatus');
INSERT INTO public.mtb_product_status VALUES (2, '非公開', 1, 'productstatus');
INSERT INTO public.mtb_product_status VALUES (3, '廃止', 2, 'productstatus');


--
-- Data for Name: mtb_rounding_type; Type: TABLE DATA; Schema: public; Owner: dbuser
--

INSERT INTO public.mtb_rounding_type VALUES (1, '四捨五入', 0, 'roundingtype');
INSERT INTO public.mtb_rounding_type VALUES (2, '切り捨て', 1, 'roundingtype');
INSERT INTO public.mtb_rounding_type VALUES (3, '切り上げ', 2, 'roundingtype');


--
-- Data for Name: mtb_sale_type; Type: TABLE DATA; Schema: public; Owner: dbuser
--

INSERT INTO public.mtb_sale_type VALUES (1, '販売種別A', 0, 'saletype');
INSERT INTO public.mtb_sale_type VALUES (2, '販売種別B', 1, 'saletype');


--
-- Data for Name: mtb_sex; Type: TABLE DATA; Schema: public; Owner: dbuser
--

INSERT INTO public.mtb_sex VALUES (1, '男性', 0, 'sex');
INSERT INTO public.mtb_sex VALUES (2, '女性', 1, 'sex');


--
-- Data for Name: mtb_tax_display_type; Type: TABLE DATA; Schema: public; Owner: dbuser
--

INSERT INTO public.mtb_tax_display_type VALUES (1, '税抜', 0, 'taxdisplaytype');
INSERT INTO public.mtb_tax_display_type VALUES (2, '税込', 1, 'taxdisplaytype');


--
-- Data for Name: mtb_tax_type; Type: TABLE DATA; Schema: public; Owner: dbuser
--

INSERT INTO public.mtb_tax_type VALUES (1, '課税', 0, 'taxtype');
INSERT INTO public.mtb_tax_type VALUES (2, '不課税', 1, 'taxtype');
INSERT INTO public.mtb_tax_type VALUES (3, '非課税', 2, 'taxtype');


--
-- Data for Name: mtb_work; Type: TABLE DATA; Schema: public; Owner: dbuser
--

INSERT INTO public.mtb_work VALUES (0, '非稼働', 0, 'work');
INSERT INTO public.mtb_work VALUES (1, '稼働', 1, 'work');


--
-- Name: dtb_authority_role_id_seq; Type: SEQUENCE SET; Schema: public; Owner: dbuser
--

SELECT pg_catalog.setval('public.dtb_authority_role_id_seq', 1, false);


--
-- Name: dtb_base_info_id_seq; Type: SEQUENCE SET; Schema: public; Owner: dbuser
--

SELECT pg_catalog.setval('public.dtb_base_info_id_seq', 1, true);


--
-- Name: dtb_block_id_seq; Type: SEQUENCE SET; Schema: public; Owner: dbuser
--

SELECT pg_catalog.setval('public.dtb_block_id_seq', 17, true);


--
-- Name: dtb_calendar_id_seq; Type: SEQUENCE SET; Schema: public; Owner: dbuser
--

SELECT pg_catalog.setval('public.dtb_calendar_id_seq', 1, false);


--
-- Name: dtb_cart_id_seq; Type: SEQUENCE SET; Schema: public; Owner: dbuser
--

SELECT pg_catalog.setval('public.dtb_cart_id_seq', 1, false);


--
-- Name: dtb_cart_item_id_seq; Type: SEQUENCE SET; Schema: public; Owner: dbuser
--

SELECT pg_catalog.setval('public.dtb_cart_item_id_seq', 1, false);


--
-- Name: dtb_category_id_seq; Type: SEQUENCE SET; Schema: public; Owner: dbuser
--

SELECT pg_catalog.setval('public.dtb_category_id_seq', 6, true);


--
-- Name: dtb_class_category_id_seq; Type: SEQUENCE SET; Schema: public; Owner: dbuser
--

SELECT pg_catalog.setval('public.dtb_class_category_id_seq', 6, true);


--
-- Name: dtb_class_name_id_seq; Type: SEQUENCE SET; Schema: public; Owner: dbuser
--

SELECT pg_catalog.setval('public.dtb_class_name_id_seq', 2, true);


--
-- Name: dtb_csv_id_seq; Type: SEQUENCE SET; Schema: public; Owner: dbuser
--

SELECT pg_catalog.setval('public.dtb_csv_id_seq', 205, true);


--
-- Name: dtb_customer_address_id_seq; Type: SEQUENCE SET; Schema: public; Owner: dbuser
--

SELECT pg_catalog.setval('public.dtb_customer_address_id_seq', 1, false);


--
-- Name: dtb_customer_favorite_product_id_seq; Type: SEQUENCE SET; Schema: public; Owner: dbuser
--

SELECT pg_catalog.setval('public.dtb_customer_favorite_product_id_seq', 1, false);


--
-- Name: dtb_customer_id_seq; Type: SEQUENCE SET; Schema: public; Owner: dbuser
--

SELECT pg_catalog.setval('public.dtb_customer_id_seq', 1, true);


--
-- Name: dtb_delivery_duration_id_seq; Type: SEQUENCE SET; Schema: public; Owner: dbuser
--

SELECT pg_catalog.setval('public.dtb_delivery_duration_id_seq', 9, true);


--
-- Name: dtb_delivery_fee_id_seq; Type: SEQUENCE SET; Schema: public; Owner: dbuser
--

SELECT pg_catalog.setval('public.dtb_delivery_fee_id_seq', 94, true);


--
-- Name: dtb_delivery_id_seq; Type: SEQUENCE SET; Schema: public; Owner: dbuser
--

SELECT pg_catalog.setval('public.dtb_delivery_id_seq', 2, true);


--
-- Name: dtb_delivery_time_id_seq; Type: SEQUENCE SET; Schema: public; Owner: dbuser
--

SELECT pg_catalog.setval('public.dtb_delivery_time_id_seq', 2, true);


--
-- Name: dtb_layout_id_seq; Type: SEQUENCE SET; Schema: public; Owner: dbuser
--

SELECT pg_catalog.setval('public.dtb_layout_id_seq', 2, true);


--
-- Name: dtb_login_history_id_seq; Type: SEQUENCE SET; Schema: public; Owner: dbuser
--

SELECT pg_catalog.setval('public.dtb_login_history_id_seq', 1, false);


--
-- Name: dtb_mail_history_id_seq; Type: SEQUENCE SET; Schema: public; Owner: dbuser
--

SELECT pg_catalog.setval('public.dtb_mail_history_id_seq', 1, false);


--
-- Name: dtb_mail_template_id_seq; Type: SEQUENCE SET; Schema: public; Owner: dbuser
--

SELECT pg_catalog.setval('public.dtb_mail_template_id_seq', 8, true);


--
-- Name: dtb_member_id_seq; Type: SEQUENCE SET; Schema: public; Owner: dbuser
--

SELECT pg_catalog.setval('public.dtb_member_id_seq', 1, true);


--
-- Name: dtb_news_id_seq; Type: SEQUENCE SET; Schema: public; Owner: dbuser
--

SELECT pg_catalog.setval('public.dtb_news_id_seq', 1, true);


--
-- Name: dtb_order_id_seq; Type: SEQUENCE SET; Schema: public; Owner: dbuser
--

SELECT pg_catalog.setval('public.dtb_order_id_seq', 1, true);


--
-- Name: dtb_order_item_id_seq; Type: SEQUENCE SET; Schema: public; Owner: dbuser
--

SELECT pg_catalog.setval('public.dtb_order_item_id_seq', 6, true);


--
-- Name: dtb_page_id_seq; Type: SEQUENCE SET; Schema: public; Owner: dbuser
--

SELECT pg_catalog.setval('public.dtb_page_id_seq', 48, true);


--
-- Name: dtb_payment_id_seq; Type: SEQUENCE SET; Schema: public; Owner: dbuser
--

SELECT pg_catalog.setval('public.dtb_payment_id_seq', 4, true);


--
-- Name: dtb_plugin_id_seq; Type: SEQUENCE SET; Schema: public; Owner: dbuser
--

SELECT pg_catalog.setval('public.dtb_plugin_id_seq', 1, false);


--
-- Name: dtb_product_class_id_seq; Type: SEQUENCE SET; Schema: public; Owner: dbuser
--

SELECT pg_catalog.setval('public.dtb_product_class_id_seq', 411, true);


--
-- Name: dtb_product_id_seq; Type: SEQUENCE SET; Schema: public; Owner: dbuser
--

SELECT pg_catalog.setval('public.dtb_product_id_seq', 102, true);


--
-- Name: dtb_product_image_id_seq; Type: SEQUENCE SET; Schema: public; Owner: dbuser
--

SELECT pg_catalog.setval('public.dtb_product_image_id_seq', 306, true);


--
-- Name: dtb_product_stock_id_seq; Type: SEQUENCE SET; Schema: public; Owner: dbuser
--

SELECT pg_catalog.setval('public.dtb_product_stock_id_seq', 411, true);


--
-- Name: dtb_product_tag_id_seq; Type: SEQUENCE SET; Schema: public; Owner: dbuser
--

SELECT pg_catalog.setval('public.dtb_product_tag_id_seq', 300, true);


--
-- Name: dtb_shipping_id_seq; Type: SEQUENCE SET; Schema: public; Owner: dbuser
--

SELECT pg_catalog.setval('public.dtb_shipping_id_seq', 1, true);


--
-- Name: dtb_tag_id_seq; Type: SEQUENCE SET; Schema: public; Owner: dbuser
--

SELECT pg_catalog.setval('public.dtb_tag_id_seq', 3, true);


--
-- Name: dtb_tax_rule_id_seq; Type: SEQUENCE SET; Schema: public; Owner: dbuser
--

SELECT pg_catalog.setval('public.dtb_tax_rule_id_seq', 1, true);


--
-- Name: dtb_template_id_seq; Type: SEQUENCE SET; Schema: public; Owner: dbuser
--

SELECT pg_catalog.setval('public.dtb_template_id_seq', 1, true);


--
-- Name: dtb_authority_role dtb_authority_role_pkey; Type: CONSTRAINT; Schema: public; Owner: dbuser
--

ALTER TABLE ONLY public.dtb_authority_role
    ADD CONSTRAINT dtb_authority_role_pkey PRIMARY KEY (id);


--
-- Name: dtb_base_info dtb_base_info_pkey; Type: CONSTRAINT; Schema: public; Owner: dbuser
--

ALTER TABLE ONLY public.dtb_base_info
    ADD CONSTRAINT dtb_base_info_pkey PRIMARY KEY (id);


--
-- Name: dtb_block dtb_block_pkey; Type: CONSTRAINT; Schema: public; Owner: dbuser
--

ALTER TABLE ONLY public.dtb_block
    ADD CONSTRAINT dtb_block_pkey PRIMARY KEY (id);


--
-- Name: dtb_block_position dtb_block_position_pkey; Type: CONSTRAINT; Schema: public; Owner: dbuser
--

ALTER TABLE ONLY public.dtb_block_position
    ADD CONSTRAINT dtb_block_position_pkey PRIMARY KEY (section, block_id, layout_id);


--
-- Name: dtb_calendar dtb_calendar_pkey; Type: CONSTRAINT; Schema: public; Owner: dbuser
--

ALTER TABLE ONLY public.dtb_calendar
    ADD CONSTRAINT dtb_calendar_pkey PRIMARY KEY (id);


--
-- Name: dtb_cart_item dtb_cart_item_pkey; Type: CONSTRAINT; Schema: public; Owner: dbuser
--

ALTER TABLE ONLY public.dtb_cart_item
    ADD CONSTRAINT dtb_cart_item_pkey PRIMARY KEY (id);


--
-- Name: dtb_cart dtb_cart_pkey; Type: CONSTRAINT; Schema: public; Owner: dbuser
--

ALTER TABLE ONLY public.dtb_cart
    ADD CONSTRAINT dtb_cart_pkey PRIMARY KEY (id);


--
-- Name: dtb_category dtb_category_pkey; Type: CONSTRAINT; Schema: public; Owner: dbuser
--

ALTER TABLE ONLY public.dtb_category
    ADD CONSTRAINT dtb_category_pkey PRIMARY KEY (id);


--
-- Name: dtb_class_category dtb_class_category_pkey; Type: CONSTRAINT; Schema: public; Owner: dbuser
--

ALTER TABLE ONLY public.dtb_class_category
    ADD CONSTRAINT dtb_class_category_pkey PRIMARY KEY (id);


--
-- Name: dtb_class_name dtb_class_name_pkey; Type: CONSTRAINT; Schema: public; Owner: dbuser
--

ALTER TABLE ONLY public.dtb_class_name
    ADD CONSTRAINT dtb_class_name_pkey PRIMARY KEY (id);


--
-- Name: dtb_csv dtb_csv_pkey; Type: CONSTRAINT; Schema: public; Owner: dbuser
--

ALTER TABLE ONLY public.dtb_csv
    ADD CONSTRAINT dtb_csv_pkey PRIMARY KEY (id);


--
-- Name: dtb_customer_address dtb_customer_address_pkey; Type: CONSTRAINT; Schema: public; Owner: dbuser
--

ALTER TABLE ONLY public.dtb_customer_address
    ADD CONSTRAINT dtb_customer_address_pkey PRIMARY KEY (id);


--
-- Name: dtb_customer_favorite_product dtb_customer_favorite_product_pkey; Type: CONSTRAINT; Schema: public; Owner: dbuser
--

ALTER TABLE ONLY public.dtb_customer_favorite_product
    ADD CONSTRAINT dtb_customer_favorite_product_pkey PRIMARY KEY (id);


--
-- Name: dtb_customer dtb_customer_pkey; Type: CONSTRAINT; Schema: public; Owner: dbuser
--

ALTER TABLE ONLY public.dtb_customer
    ADD CONSTRAINT dtb_customer_pkey PRIMARY KEY (id);


--
-- Name: dtb_delivery_duration dtb_delivery_duration_pkey; Type: CONSTRAINT; Schema: public; Owner: dbuser
--

ALTER TABLE ONLY public.dtb_delivery_duration
    ADD CONSTRAINT dtb_delivery_duration_pkey PRIMARY KEY (id);


--
-- Name: dtb_delivery_fee dtb_delivery_fee_pkey; Type: CONSTRAINT; Schema: public; Owner: dbuser
--

ALTER TABLE ONLY public.dtb_delivery_fee
    ADD CONSTRAINT dtb_delivery_fee_pkey PRIMARY KEY (id);


--
-- Name: dtb_delivery dtb_delivery_pkey; Type: CONSTRAINT; Schema: public; Owner: dbuser
--

ALTER TABLE ONLY public.dtb_delivery
    ADD CONSTRAINT dtb_delivery_pkey PRIMARY KEY (id);


--
-- Name: dtb_delivery_time dtb_delivery_time_pkey; Type: CONSTRAINT; Schema: public; Owner: dbuser
--

ALTER TABLE ONLY public.dtb_delivery_time
    ADD CONSTRAINT dtb_delivery_time_pkey PRIMARY KEY (id);


--
-- Name: dtb_layout dtb_layout_pkey; Type: CONSTRAINT; Schema: public; Owner: dbuser
--

ALTER TABLE ONLY public.dtb_layout
    ADD CONSTRAINT dtb_layout_pkey PRIMARY KEY (id);


--
-- Name: dtb_login_history dtb_login_history_pkey; Type: CONSTRAINT; Schema: public; Owner: dbuser
--

ALTER TABLE ONLY public.dtb_login_history
    ADD CONSTRAINT dtb_login_history_pkey PRIMARY KEY (id);


--
-- Name: dtb_mail_history dtb_mail_history_pkey; Type: CONSTRAINT; Schema: public; Owner: dbuser
--

ALTER TABLE ONLY public.dtb_mail_history
    ADD CONSTRAINT dtb_mail_history_pkey PRIMARY KEY (id);


--
-- Name: dtb_mail_template dtb_mail_template_pkey; Type: CONSTRAINT; Schema: public; Owner: dbuser
--

ALTER TABLE ONLY public.dtb_mail_template
    ADD CONSTRAINT dtb_mail_template_pkey PRIMARY KEY (id);


--
-- Name: dtb_member dtb_member_pkey; Type: CONSTRAINT; Schema: public; Owner: dbuser
--

ALTER TABLE ONLY public.dtb_member
    ADD CONSTRAINT dtb_member_pkey PRIMARY KEY (id);


--
-- Name: dtb_news dtb_news_pkey; Type: CONSTRAINT; Schema: public; Owner: dbuser
--

ALTER TABLE ONLY public.dtb_news
    ADD CONSTRAINT dtb_news_pkey PRIMARY KEY (id);


--
-- Name: dtb_order_item dtb_order_item_pkey; Type: CONSTRAINT; Schema: public; Owner: dbuser
--

ALTER TABLE ONLY public.dtb_order_item
    ADD CONSTRAINT dtb_order_item_pkey PRIMARY KEY (id);


--
-- Name: dtb_order_pdf dtb_order_pdf_pkey; Type: CONSTRAINT; Schema: public; Owner: dbuser
--

ALTER TABLE ONLY public.dtb_order_pdf
    ADD CONSTRAINT dtb_order_pdf_pkey PRIMARY KEY (member_id);


--
-- Name: dtb_order dtb_order_pkey; Type: CONSTRAINT; Schema: public; Owner: dbuser
--

ALTER TABLE ONLY public.dtb_order
    ADD CONSTRAINT dtb_order_pkey PRIMARY KEY (id);


--
-- Name: dtb_page_layout dtb_page_layout_pkey; Type: CONSTRAINT; Schema: public; Owner: dbuser
--

ALTER TABLE ONLY public.dtb_page_layout
    ADD CONSTRAINT dtb_page_layout_pkey PRIMARY KEY (page_id, layout_id);


--
-- Name: dtb_page dtb_page_pkey; Type: CONSTRAINT; Schema: public; Owner: dbuser
--

ALTER TABLE ONLY public.dtb_page
    ADD CONSTRAINT dtb_page_pkey PRIMARY KEY (id);


--
-- Name: dtb_payment_option dtb_payment_option_pkey; Type: CONSTRAINT; Schema: public; Owner: dbuser
--

ALTER TABLE ONLY public.dtb_payment_option
    ADD CONSTRAINT dtb_payment_option_pkey PRIMARY KEY (delivery_id, payment_id);


--
-- Name: dtb_payment dtb_payment_pkey; Type: CONSTRAINT; Schema: public; Owner: dbuser
--

ALTER TABLE ONLY public.dtb_payment
    ADD CONSTRAINT dtb_payment_pkey PRIMARY KEY (id);


--
-- Name: dtb_plugin dtb_plugin_pkey; Type: CONSTRAINT; Schema: public; Owner: dbuser
--

ALTER TABLE ONLY public.dtb_plugin
    ADD CONSTRAINT dtb_plugin_pkey PRIMARY KEY (id);


--
-- Name: dtb_product_category dtb_product_category_pkey; Type: CONSTRAINT; Schema: public; Owner: dbuser
--

ALTER TABLE ONLY public.dtb_product_category
    ADD CONSTRAINT dtb_product_category_pkey PRIMARY KEY (product_id, category_id);


--
-- Name: dtb_product_class dtb_product_class_pkey; Type: CONSTRAINT; Schema: public; Owner: dbuser
--

ALTER TABLE ONLY public.dtb_product_class
    ADD CONSTRAINT dtb_product_class_pkey PRIMARY KEY (id);


--
-- Name: dtb_product_image dtb_product_image_pkey; Type: CONSTRAINT; Schema: public; Owner: dbuser
--

ALTER TABLE ONLY public.dtb_product_image
    ADD CONSTRAINT dtb_product_image_pkey PRIMARY KEY (id);


--
-- Name: dtb_product dtb_product_pkey; Type: CONSTRAINT; Schema: public; Owner: dbuser
--

ALTER TABLE ONLY public.dtb_product
    ADD CONSTRAINT dtb_product_pkey PRIMARY KEY (id);


--
-- Name: dtb_product_stock dtb_product_stock_pkey; Type: CONSTRAINT; Schema: public; Owner: dbuser
--

ALTER TABLE ONLY public.dtb_product_stock
    ADD CONSTRAINT dtb_product_stock_pkey PRIMARY KEY (id);


--
-- Name: dtb_product_tag dtb_product_tag_pkey; Type: CONSTRAINT; Schema: public; Owner: dbuser
--

ALTER TABLE ONLY public.dtb_product_tag
    ADD CONSTRAINT dtb_product_tag_pkey PRIMARY KEY (id);


--
-- Name: dtb_shipping dtb_shipping_pkey; Type: CONSTRAINT; Schema: public; Owner: dbuser
--

ALTER TABLE ONLY public.dtb_shipping
    ADD CONSTRAINT dtb_shipping_pkey PRIMARY KEY (id);


--
-- Name: dtb_tag dtb_tag_pkey; Type: CONSTRAINT; Schema: public; Owner: dbuser
--

ALTER TABLE ONLY public.dtb_tag
    ADD CONSTRAINT dtb_tag_pkey PRIMARY KEY (id);


--
-- Name: dtb_tax_rule dtb_tax_rule_pkey; Type: CONSTRAINT; Schema: public; Owner: dbuser
--

ALTER TABLE ONLY public.dtb_tax_rule
    ADD CONSTRAINT dtb_tax_rule_pkey PRIMARY KEY (id);


--
-- Name: dtb_template dtb_template_pkey; Type: CONSTRAINT; Schema: public; Owner: dbuser
--

ALTER TABLE ONLY public.dtb_template
    ADD CONSTRAINT dtb_template_pkey PRIMARY KEY (id);


--
-- Name: mtb_authority mtb_authority_pkey; Type: CONSTRAINT; Schema: public; Owner: dbuser
--

ALTER TABLE ONLY public.mtb_authority
    ADD CONSTRAINT mtb_authority_pkey PRIMARY KEY (id);


--
-- Name: mtb_country mtb_country_pkey; Type: CONSTRAINT; Schema: public; Owner: dbuser
--

ALTER TABLE ONLY public.mtb_country
    ADD CONSTRAINT mtb_country_pkey PRIMARY KEY (id);


--
-- Name: mtb_csv_type mtb_csv_type_pkey; Type: CONSTRAINT; Schema: public; Owner: dbuser
--

ALTER TABLE ONLY public.mtb_csv_type
    ADD CONSTRAINT mtb_csv_type_pkey PRIMARY KEY (id);


--
-- Name: mtb_customer_order_status mtb_customer_order_status_pkey; Type: CONSTRAINT; Schema: public; Owner: dbuser
--

ALTER TABLE ONLY public.mtb_customer_order_status
    ADD CONSTRAINT mtb_customer_order_status_pkey PRIMARY KEY (id);


--
-- Name: mtb_customer_status mtb_customer_status_pkey; Type: CONSTRAINT; Schema: public; Owner: dbuser
--

ALTER TABLE ONLY public.mtb_customer_status
    ADD CONSTRAINT mtb_customer_status_pkey PRIMARY KEY (id);


--
-- Name: mtb_device_type mtb_device_type_pkey; Type: CONSTRAINT; Schema: public; Owner: dbuser
--

ALTER TABLE ONLY public.mtb_device_type
    ADD CONSTRAINT mtb_device_type_pkey PRIMARY KEY (id);


--
-- Name: mtb_job mtb_job_pkey; Type: CONSTRAINT; Schema: public; Owner: dbuser
--

ALTER TABLE ONLY public.mtb_job
    ADD CONSTRAINT mtb_job_pkey PRIMARY KEY (id);


--
-- Name: mtb_login_history_status mtb_login_history_status_pkey; Type: CONSTRAINT; Schema: public; Owner: dbuser
--

ALTER TABLE ONLY public.mtb_login_history_status
    ADD CONSTRAINT mtb_login_history_status_pkey PRIMARY KEY (id);


--
-- Name: mtb_order_item_type mtb_order_item_type_pkey; Type: CONSTRAINT; Schema: public; Owner: dbuser
--

ALTER TABLE ONLY public.mtb_order_item_type
    ADD CONSTRAINT mtb_order_item_type_pkey PRIMARY KEY (id);


--
-- Name: mtb_order_status_color mtb_order_status_color_pkey; Type: CONSTRAINT; Schema: public; Owner: dbuser
--

ALTER TABLE ONLY public.mtb_order_status_color
    ADD CONSTRAINT mtb_order_status_color_pkey PRIMARY KEY (id);


--
-- Name: mtb_order_status mtb_order_status_pkey; Type: CONSTRAINT; Schema: public; Owner: dbuser
--

ALTER TABLE ONLY public.mtb_order_status
    ADD CONSTRAINT mtb_order_status_pkey PRIMARY KEY (id);


--
-- Name: mtb_page_max mtb_page_max_pkey; Type: CONSTRAINT; Schema: public; Owner: dbuser
--

ALTER TABLE ONLY public.mtb_page_max
    ADD CONSTRAINT mtb_page_max_pkey PRIMARY KEY (id);


--
-- Name: mtb_pref mtb_pref_pkey; Type: CONSTRAINT; Schema: public; Owner: dbuser
--

ALTER TABLE ONLY public.mtb_pref
    ADD CONSTRAINT mtb_pref_pkey PRIMARY KEY (id);


--
-- Name: mtb_product_list_max mtb_product_list_max_pkey; Type: CONSTRAINT; Schema: public; Owner: dbuser
--

ALTER TABLE ONLY public.mtb_product_list_max
    ADD CONSTRAINT mtb_product_list_max_pkey PRIMARY KEY (id);


--
-- Name: mtb_product_list_order_by mtb_product_list_order_by_pkey; Type: CONSTRAINT; Schema: public; Owner: dbuser
--

ALTER TABLE ONLY public.mtb_product_list_order_by
    ADD CONSTRAINT mtb_product_list_order_by_pkey PRIMARY KEY (id);


--
-- Name: mtb_product_status mtb_product_status_pkey; Type: CONSTRAINT; Schema: public; Owner: dbuser
--

ALTER TABLE ONLY public.mtb_product_status
    ADD CONSTRAINT mtb_product_status_pkey PRIMARY KEY (id);


--
-- Name: mtb_rounding_type mtb_rounding_type_pkey; Type: CONSTRAINT; Schema: public; Owner: dbuser
--

ALTER TABLE ONLY public.mtb_rounding_type
    ADD CONSTRAINT mtb_rounding_type_pkey PRIMARY KEY (id);


--
-- Name: mtb_sale_type mtb_sale_type_pkey; Type: CONSTRAINT; Schema: public; Owner: dbuser
--

ALTER TABLE ONLY public.mtb_sale_type
    ADD CONSTRAINT mtb_sale_type_pkey PRIMARY KEY (id);


--
-- Name: mtb_sex mtb_sex_pkey; Type: CONSTRAINT; Schema: public; Owner: dbuser
--

ALTER TABLE ONLY public.mtb_sex
    ADD CONSTRAINT mtb_sex_pkey PRIMARY KEY (id);


--
-- Name: mtb_tax_display_type mtb_tax_display_type_pkey; Type: CONSTRAINT; Schema: public; Owner: dbuser
--

ALTER TABLE ONLY public.mtb_tax_display_type
    ADD CONSTRAINT mtb_tax_display_type_pkey PRIMARY KEY (id);


--
-- Name: mtb_tax_type mtb_tax_type_pkey; Type: CONSTRAINT; Schema: public; Owner: dbuser
--

ALTER TABLE ONLY public.mtb_tax_type
    ADD CONSTRAINT mtb_tax_type_pkey PRIMARY KEY (id);


--
-- Name: mtb_work mtb_work_pkey; Type: CONSTRAINT; Schema: public; Owner: dbuser
--

ALTER TABLE ONLY public.mtb_work
    ADD CONSTRAINT mtb_work_pkey PRIMARY KEY (id);


--
-- Name: device_type_id; Type: INDEX; Schema: public; Owner: dbuser
--

CREATE UNIQUE INDEX device_type_id ON public.dtb_block USING btree (device_type_id, file_name);


--
-- Name: dtb_cart_pre_order_id_idx; Type: INDEX; Schema: public; Owner: dbuser
--

CREATE UNIQUE INDEX dtb_cart_pre_order_id_idx ON public.dtb_cart USING btree (pre_order_id);


--
-- Name: dtb_cart_update_date_idx; Type: INDEX; Schema: public; Owner: dbuser
--

CREATE INDEX dtb_cart_update_date_idx ON public.dtb_cart USING btree (update_date);


--
-- Name: dtb_customer_buy_times_idx; Type: INDEX; Schema: public; Owner: dbuser
--

CREATE INDEX dtb_customer_buy_times_idx ON public.dtb_customer USING btree (buy_times);


--
-- Name: dtb_customer_buy_total_idx; Type: INDEX; Schema: public; Owner: dbuser
--

CREATE INDEX dtb_customer_buy_total_idx ON public.dtb_customer USING btree (buy_total);


--
-- Name: dtb_customer_create_date_idx; Type: INDEX; Schema: public; Owner: dbuser
--

CREATE INDEX dtb_customer_create_date_idx ON public.dtb_customer USING btree (create_date);


--
-- Name: dtb_customer_email_idx; Type: INDEX; Schema: public; Owner: dbuser
--

CREATE INDEX dtb_customer_email_idx ON public.dtb_customer USING btree (email);


--
-- Name: dtb_customer_last_buy_date_idx; Type: INDEX; Schema: public; Owner: dbuser
--

CREATE INDEX dtb_customer_last_buy_date_idx ON public.dtb_customer USING btree (last_buy_date);


--
-- Name: dtb_customer_update_date_idx; Type: INDEX; Schema: public; Owner: dbuser
--

CREATE INDEX dtb_customer_update_date_idx ON public.dtb_customer USING btree (update_date);


--
-- Name: dtb_order_email_idx; Type: INDEX; Schema: public; Owner: dbuser
--

CREATE INDEX dtb_order_email_idx ON public.dtb_order USING btree (email);


--
-- Name: dtb_order_order_date_idx; Type: INDEX; Schema: public; Owner: dbuser
--

CREATE INDEX dtb_order_order_date_idx ON public.dtb_order USING btree (order_date);


--
-- Name: dtb_order_order_no_idx; Type: INDEX; Schema: public; Owner: dbuser
--

CREATE INDEX dtb_order_order_no_idx ON public.dtb_order USING btree (order_no);


--
-- Name: dtb_order_payment_date_idx; Type: INDEX; Schema: public; Owner: dbuser
--

CREATE INDEX dtb_order_payment_date_idx ON public.dtb_order USING btree (payment_date);


--
-- Name: dtb_order_pre_order_id_idx; Type: INDEX; Schema: public; Owner: dbuser
--

CREATE UNIQUE INDEX dtb_order_pre_order_id_idx ON public.dtb_order USING btree (pre_order_id);


--
-- Name: dtb_order_update_date_idx; Type: INDEX; Schema: public; Owner: dbuser
--

CREATE INDEX dtb_order_update_date_idx ON public.dtb_order USING btree (update_date);


--
-- Name: dtb_page_url_idx; Type: INDEX; Schema: public; Owner: dbuser
--

CREATE INDEX dtb_page_url_idx ON public.dtb_page USING btree (url);


--
-- Name: dtb_product_class_price02_idx; Type: INDEX; Schema: public; Owner: dbuser
--

CREATE INDEX dtb_product_class_price02_idx ON public.dtb_product_class USING btree (price02);


--
-- Name: dtb_product_class_stock_stock_unlimited_idx; Type: INDEX; Schema: public; Owner: dbuser
--

CREATE INDEX dtb_product_class_stock_stock_unlimited_idx ON public.dtb_product_class USING btree (stock, stock_unlimited);


--
-- Name: idx_10bc3be661220ea6; Type: INDEX; Schema: public; Owner: dbuser
--

CREATE INDEX idx_10bc3be661220ea6 ON public.dtb_member USING btree (creator_id);


--
-- Name: idx_10bc3be681ec865b; Type: INDEX; Schema: public; Owner: dbuser
--

CREATE INDEX idx_10bc3be681ec865b ON public.dtb_member USING btree (authority_id);


--
-- Name: idx_10bc3be6bb3453db; Type: INDEX; Schema: public; Owner: dbuser
--

CREATE INDEX idx_10bc3be6bb3453db ON public.dtb_member USING btree (work_id);


--
-- Name: idx_187c95ad61220ea6; Type: INDEX; Schema: public; Owner: dbuser
--

CREATE INDEX idx_187c95ad61220ea6 ON public.dtb_class_name USING btree (creator_id);


--
-- Name: idx_1a11d1ba248d128; Type: INDEX; Schema: public; Owner: dbuser
--

CREATE INDEX idx_1a11d1ba248d128 ON public.dtb_product_class USING btree (class_category_id1);


--
-- Name: idx_1a11d1ba4584665a; Type: INDEX; Schema: public; Owner: dbuser
--

CREATE INDEX idx_1a11d1ba4584665a ON public.dtb_product_class USING btree (product_id);


--
-- Name: idx_1a11d1ba61220ea6; Type: INDEX; Schema: public; Owner: dbuser
--

CREATE INDEX idx_1a11d1ba61220ea6 ON public.dtb_product_class USING btree (creator_id);


--
-- Name: idx_1a11d1ba9b418092; Type: INDEX; Schema: public; Owner: dbuser
--

CREATE INDEX idx_1a11d1ba9b418092 ON public.dtb_product_class USING btree (class_category_id2);


--
-- Name: idx_1a11d1bab0524e01; Type: INDEX; Schema: public; Owner: dbuser
--

CREATE INDEX idx_1a11d1bab0524e01 ON public.dtb_product_class USING btree (sale_type_id);


--
-- Name: idx_1a11d1baba4269e; Type: INDEX; Schema: public; Owner: dbuser
--

CREATE INDEX idx_1a11d1baba4269e ON public.dtb_product_class USING btree (delivery_duration_id);


--
-- Name: idx_1cb16db261220ea6; Type: INDEX; Schema: public; Owner: dbuser
--

CREATE INDEX idx_1cb16db261220ea6 ON public.dtb_mail_template USING btree (creator_id);


--
-- Name: idx_1d3655f4e171ef5f; Type: INDEX; Schema: public; Owner: dbuser
--

CREATE INDEX idx_1d3655f4e171ef5f ON public.dtb_base_info USING btree (pref_id);


--
-- Name: idx_1d3655f4f92f3e70; Type: INDEX; Schema: public; Owner: dbuser
--

CREATE INDEX idx_1d3655f4f92f3e70 ON public.dtb_base_info USING btree (country_id);


--
-- Name: idx_1d66d8074c3a3bb; Type: INDEX; Schema: public; Owner: dbuser
--

CREATE INDEX idx_1d66d8074c3a3bb ON public.dtb_order USING btree (payment_id);


--
-- Name: idx_1d66d8074ffa550e; Type: INDEX; Schema: public; Owner: dbuser
--

CREATE INDEX idx_1d66d8074ffa550e ON public.dtb_order USING btree (device_type_id);


--
-- Name: idx_1d66d8075a2db2a0; Type: INDEX; Schema: public; Owner: dbuser
--

CREATE INDEX idx_1d66d8075a2db2a0 ON public.dtb_order USING btree (sex_id);


--
-- Name: idx_1d66d8079395c3f3; Type: INDEX; Schema: public; Owner: dbuser
--

CREATE INDEX idx_1d66d8079395c3f3 ON public.dtb_order USING btree (customer_id);


--
-- Name: idx_1d66d807be04ea9; Type: INDEX; Schema: public; Owner: dbuser
--

CREATE INDEX idx_1d66d807be04ea9 ON public.dtb_order USING btree (job_id);


--
-- Name: idx_1d66d807d7707b45; Type: INDEX; Schema: public; Owner: dbuser
--

CREATE INDEX idx_1d66d807d7707b45 ON public.dtb_order USING btree (order_status_id);


--
-- Name: idx_1d66d807e171ef5f; Type: INDEX; Schema: public; Owner: dbuser
--

CREATE INDEX idx_1d66d807e171ef5f ON public.dtb_order USING btree (pref_id);


--
-- Name: idx_1d66d807f92f3e70; Type: INDEX; Schema: public; Owner: dbuser
--

CREATE INDEX idx_1d66d807f92f3e70 ON public.dtb_order USING btree (country_id);


--
-- Name: idx_2ebd22ce12136921; Type: INDEX; Schema: public; Owner: dbuser
--

CREATE INDEX idx_2ebd22ce12136921 ON public.dtb_shipping USING btree (delivery_id);


--
-- Name: idx_2ebd22ce61220ea6; Type: INDEX; Schema: public; Owner: dbuser
--

CREATE INDEX idx_2ebd22ce61220ea6 ON public.dtb_shipping USING btree (creator_id);


--
-- Name: idx_2ebd22ce8d9f6d38; Type: INDEX; Schema: public; Owner: dbuser
--

CREATE INDEX idx_2ebd22ce8d9f6d38 ON public.dtb_shipping USING btree (order_id);


--
-- Name: idx_2ebd22cee171ef5f; Type: INDEX; Schema: public; Owner: dbuser
--

CREATE INDEX idx_2ebd22cee171ef5f ON public.dtb_shipping USING btree (pref_id);


--
-- Name: idx_2ebd22cef92f3e70; Type: INDEX; Schema: public; Owner: dbuser
--

CREATE INDEX idx_2ebd22cef92f3e70 ON public.dtb_shipping USING btree (country_id);


--
-- Name: idx_3267cc7a4584665a; Type: INDEX; Schema: public; Owner: dbuser
--

CREATE INDEX idx_3267cc7a4584665a ON public.dtb_product_image USING btree (product_id);


--
-- Name: idx_3267cc7a61220ea6; Type: INDEX; Schema: public; Owner: dbuser
--

CREATE INDEX idx_3267cc7a61220ea6 ON public.dtb_product_image USING btree (creator_id);


--
-- Name: idx_3420d9fa61220ea6; Type: INDEX; Schema: public; Owner: dbuser
--

CREATE INDEX idx_3420d9fa61220ea6 ON public.dtb_delivery USING btree (creator_id);


--
-- Name: idx_3420d9fab0524e01; Type: INDEX; Schema: public; Owner: dbuser
--

CREATE INDEX idx_3420d9fab0524e01 ON public.dtb_delivery USING btree (sale_type_id);


--
-- Name: idx_35dcd7318c22aa1a; Type: INDEX; Schema: public; Owner: dbuser
--

CREATE INDEX idx_35dcd7318c22aa1a ON public.dtb_block_position USING btree (layout_id);


--
-- Name: idx_35dcd731e9ed820c; Type: INDEX; Schema: public; Owner: dbuser
--

CREATE INDEX idx_35dcd731e9ed820c ON public.dtb_block_position USING btree (block_id);


--
-- Name: idx_4433e7214584665a; Type: INDEX; Schema: public; Owner: dbuser
--

CREATE INDEX idx_4433e7214584665a ON public.dtb_product_tag USING btree (product_id);


--
-- Name: idx_4433e72161220ea6; Type: INDEX; Schema: public; Owner: dbuser
--

CREATE INDEX idx_4433e72161220ea6 ON public.dtb_product_tag USING btree (creator_id);


--
-- Name: idx_4433e721bad26311; Type: INDEX; Schema: public; Owner: dbuser
--

CREATE INDEX idx_4433e721bad26311 ON public.dtb_product_tag USING btree (tag_id);


--
-- Name: idx_4870ab1161220ea6; Type: INDEX; Schema: public; Owner: dbuser
--

CREATE INDEX idx_4870ab1161220ea6 ON public.dtb_mail_history USING btree (creator_id);


--
-- Name: idx_4870ab118d9f6d38; Type: INDEX; Schema: public; Owner: dbuser
--

CREATE INDEX idx_4870ab118d9f6d38 ON public.dtb_mail_history USING btree (order_id);


--
-- Name: idx_491552412136921; Type: INDEX; Schema: public; Owner: dbuser
--

CREATE INDEX idx_491552412136921 ON public.dtb_delivery_fee USING btree (delivery_id);


--
-- Name: idx_4915524e171ef5f; Type: INDEX; Schema: public; Owner: dbuser
--

CREATE INDEX idx_4915524e171ef5f ON public.dtb_delivery_fee USING btree (pref_id);


--
-- Name: idx_4a1f70b161220ea6; Type: INDEX; Schema: public; Owner: dbuser
--

CREATE INDEX idx_4a1f70b161220ea6 ON public.dtb_authority_role USING btree (creator_id);


--
-- Name: idx_4a1f70b181ec865b; Type: INDEX; Schema: public; Owner: dbuser
--

CREATE INDEX idx_4a1f70b181ec865b ON public.dtb_authority_role USING btree (authority_id);


--
-- Name: idx_5631540d12136921; Type: INDEX; Schema: public; Owner: dbuser
--

CREATE INDEX idx_5631540d12136921 ON public.dtb_payment_option USING btree (delivery_id);


--
-- Name: idx_5631540d4c3a3bb; Type: INDEX; Schema: public; Owner: dbuser
--

CREATE INDEX idx_5631540d4c3a3bb ON public.dtb_payment_option USING btree (payment_id);


--
-- Name: idx_59f696de1bd5c574; Type: INDEX; Schema: public; Owner: dbuser
--

CREATE INDEX idx_59f696de1bd5c574 ON public.dtb_tax_rule USING btree (rounding_type_id);


--
-- Name: idx_59f696de21b06187; Type: INDEX; Schema: public; Owner: dbuser
--

CREATE INDEX idx_59f696de21b06187 ON public.dtb_tax_rule USING btree (product_class_id);


--
-- Name: idx_59f696de4584665a; Type: INDEX; Schema: public; Owner: dbuser
--

CREATE INDEX idx_59f696de4584665a ON public.dtb_tax_rule USING btree (product_id);


--
-- Name: idx_59f696de61220ea6; Type: INDEX; Schema: public; Owner: dbuser
--

CREATE INDEX idx_59f696de61220ea6 ON public.dtb_tax_rule USING btree (creator_id);


--
-- Name: idx_59f696dee171ef5f; Type: INDEX; Schema: public; Owner: dbuser
--

CREATE INDEX idx_59f696dee171ef5f ON public.dtb_tax_rule USING btree (pref_id);


--
-- Name: idx_59f696def92f3e70; Type: INDEX; Schema: public; Owner: dbuser
--

CREATE INDEX idx_59f696def92f3e70 ON public.dtb_tax_rule USING btree (country_id);


--
-- Name: idx_5a62aa7c4ffa550e; Type: INDEX; Schema: public; Owner: dbuser
--

CREATE INDEX idx_5a62aa7c4ffa550e ON public.dtb_layout USING btree (device_type_id);


--
-- Name: idx_5ed2c2b61220ea6; Type: INDEX; Schema: public; Owner: dbuser
--

CREATE INDEX idx_5ed2c2b61220ea6 ON public.dtb_category USING btree (creator_id);


--
-- Name: idx_5ed2c2b796a8f92; Type: INDEX; Schema: public; Owner: dbuser
--

CREATE INDEX idx_5ed2c2b796a8f92 ON public.dtb_category USING btree (parent_category_id);


--
-- Name: idx_6191dd4f7597d3fe; Type: INDEX; Schema: public; Owner: dbuser
--

CREATE INDEX idx_6191dd4f7597d3fe ON public.dtb_login_history USING btree (member_id);


--
-- Name: idx_6191dd4f9fa62fdd; Type: INDEX; Schema: public; Owner: dbuser
--

CREATE INDEX idx_6191dd4f9fa62fdd ON public.dtb_login_history USING btree (login_history_status_id);


--
-- Name: idx_6b54dcbd4ffa550e; Type: INDEX; Schema: public; Owner: dbuser
--

CREATE INDEX idx_6b54dcbd4ffa550e ON public.dtb_block USING btree (device_type_id);


--
-- Name: idx_6c38c0f89395c3f3; Type: INDEX; Schema: public; Owner: dbuser
--

CREATE INDEX idx_6c38c0f89395c3f3 ON public.dtb_customer_address USING btree (customer_id);


--
-- Name: idx_6c38c0f8e171ef5f; Type: INDEX; Schema: public; Owner: dbuser
--

CREATE INDEX idx_6c38c0f8e171ef5f ON public.dtb_customer_address USING btree (pref_id);


--
-- Name: idx_6c38c0f8f92f3e70; Type: INDEX; Schema: public; Owner: dbuser
--

CREATE INDEX idx_6c38c0f8f92f3e70 ON public.dtb_customer_address USING btree (country_id);


--
-- Name: idx_7aff628f61220ea6; Type: INDEX; Schema: public; Owner: dbuser
--

CREATE INDEX idx_7aff628f61220ea6 ON public.dtb_payment USING btree (creator_id);


--
-- Name: idx_8298bbe35a2db2a0; Type: INDEX; Schema: public; Owner: dbuser
--

CREATE INDEX idx_8298bbe35a2db2a0 ON public.dtb_customer USING btree (sex_id);


--
-- Name: idx_8298bbe3be04ea9; Type: INDEX; Schema: public; Owner: dbuser
--

CREATE INDEX idx_8298bbe3be04ea9 ON public.dtb_customer USING btree (job_id);


--
-- Name: idx_8298bbe3c00af8a7; Type: INDEX; Schema: public; Owner: dbuser
--

CREATE INDEX idx_8298bbe3c00af8a7 ON public.dtb_customer USING btree (customer_status_id);


--
-- Name: idx_8298bbe3e171ef5f; Type: INDEX; Schema: public; Owner: dbuser
--

CREATE INDEX idx_8298bbe3e171ef5f ON public.dtb_customer USING btree (pref_id);


--
-- Name: idx_8298bbe3f92f3e70; Type: INDEX; Schema: public; Owner: dbuser
--

CREATE INDEX idx_8298bbe3f92f3e70 ON public.dtb_customer USING btree (country_id);


--
-- Name: idx_94c12a694ffa550e; Type: INDEX; Schema: public; Owner: dbuser
--

CREATE INDEX idx_94c12a694ffa550e ON public.dtb_template USING btree (device_type_id);


--
-- Name: idx_9b0d1dba61220ea6; Type: INDEX; Schema: public; Owner: dbuser
--

CREATE INDEX idx_9b0d1dba61220ea6 ON public.dtb_class_category USING btree (creator_id);


--
-- Name: idx_9b0d1dbab462fb2a; Type: INDEX; Schema: public; Owner: dbuser
--

CREATE INDEX idx_9b0d1dbab462fb2a ON public.dtb_class_category USING btree (class_name_id);


--
-- Name: idx_a0c8c3ed1bd5c574; Type: INDEX; Schema: public; Owner: dbuser
--

CREATE INDEX idx_a0c8c3ed1bd5c574 ON public.dtb_order_item USING btree (rounding_type_id);


--
-- Name: idx_a0c8c3ed21b06187; Type: INDEX; Schema: public; Owner: dbuser
--

CREATE INDEX idx_a0c8c3ed21b06187 ON public.dtb_order_item USING btree (product_class_id);


--
-- Name: idx_a0c8c3ed4584665a; Type: INDEX; Schema: public; Owner: dbuser
--

CREATE INDEX idx_a0c8c3ed4584665a ON public.dtb_order_item USING btree (product_id);


--
-- Name: idx_a0c8c3ed4887f3f8; Type: INDEX; Schema: public; Owner: dbuser
--

CREATE INDEX idx_a0c8c3ed4887f3f8 ON public.dtb_order_item USING btree (shipping_id);


--
-- Name: idx_a0c8c3ed84042c99; Type: INDEX; Schema: public; Owner: dbuser
--

CREATE INDEX idx_a0c8c3ed84042c99 ON public.dtb_order_item USING btree (tax_type_id);


--
-- Name: idx_a0c8c3ed8d9f6d38; Type: INDEX; Schema: public; Owner: dbuser
--

CREATE INDEX idx_a0c8c3ed8d9f6d38 ON public.dtb_order_item USING btree (order_id);


--
-- Name: idx_a0c8c3eda2505856; Type: INDEX; Schema: public; Owner: dbuser
--

CREATE INDEX idx_a0c8c3eda2505856 ON public.dtb_order_item USING btree (tax_display_type_id);


--
-- Name: idx_a0c8c3edcad13ead; Type: INDEX; Schema: public; Owner: dbuser
--

CREATE INDEX idx_a0c8c3edcad13ead ON public.dtb_order_item USING btree (order_item_type_id);


--
-- Name: idx_b0228f741ad5cdbf; Type: INDEX; Schema: public; Owner: dbuser
--

CREATE INDEX idx_b0228f741ad5cdbf ON public.dtb_cart_item USING btree (cart_id);


--
-- Name: idx_b0228f7421b06187; Type: INDEX; Schema: public; Owner: dbuser
--

CREATE INDEX idx_b0228f7421b06187 ON public.dtb_cart_item USING btree (product_class_id);


--
-- Name: idx_b057789112469de2; Type: INDEX; Schema: public; Owner: dbuser
--

CREATE INDEX idx_b057789112469de2 ON public.dtb_product_category USING btree (category_id);


--
-- Name: idx_b05778914584665a; Type: INDEX; Schema: public; Owner: dbuser
--

CREATE INDEX idx_b05778914584665a ON public.dtb_product_category USING btree (product_id);


--
-- Name: idx_bc6c9e4521b06187; Type: INDEX; Schema: public; Owner: dbuser
--

CREATE INDEX idx_bc6c9e4521b06187 ON public.dtb_product_stock USING btree (product_class_id);


--
-- Name: idx_bc6c9e4561220ea6; Type: INDEX; Schema: public; Owner: dbuser
--

CREATE INDEX idx_bc6c9e4561220ea6 ON public.dtb_product_stock USING btree (creator_id);


--
-- Name: idx_c49de22f557b630; Type: INDEX; Schema: public; Owner: dbuser
--

CREATE INDEX idx_c49de22f557b630 ON public.dtb_product USING btree (product_status_id);


--
-- Name: idx_c49de22f61220ea6; Type: INDEX; Schema: public; Owner: dbuser
--

CREATE INDEX idx_c49de22f61220ea6 ON public.dtb_product USING btree (creator_id);


--
-- Name: idx_e3951a67d0618e8c; Type: INDEX; Schema: public; Owner: dbuser
--

CREATE INDEX idx_e3951a67d0618e8c ON public.dtb_page USING btree (master_page_id);


--
-- Name: idx_e80ee3a612136921; Type: INDEX; Schema: public; Owner: dbuser
--

CREATE INDEX idx_e80ee3a612136921 ON public.dtb_delivery_time USING btree (delivery_id);


--
-- Name: idx_ea4c351761220ea6; Type: INDEX; Schema: public; Owner: dbuser
--

CREATE INDEX idx_ea4c351761220ea6 ON public.dtb_news USING btree (creator_id);


--
-- Name: idx_ed6313834584665a; Type: INDEX; Schema: public; Owner: dbuser
--

CREATE INDEX idx_ed6313834584665a ON public.dtb_customer_favorite_product USING btree (product_id);


--
-- Name: idx_ed6313839395c3f3; Type: INDEX; Schema: public; Owner: dbuser
--

CREATE INDEX idx_ed6313839395c3f3 ON public.dtb_customer_favorite_product USING btree (customer_id);


--
-- Name: idx_f27999418c22aa1a; Type: INDEX; Schema: public; Owner: dbuser
--

CREATE INDEX idx_f27999418c22aa1a ON public.dtb_page_layout USING btree (layout_id);


--
-- Name: idx_f2799941c4663e4; Type: INDEX; Schema: public; Owner: dbuser
--

CREATE INDEX idx_f2799941c4663e4 ON public.dtb_page_layout USING btree (page_id);


--
-- Name: idx_f55f48c361220ea6; Type: INDEX; Schema: public; Owner: dbuser
--

CREATE INDEX idx_f55f48c361220ea6 ON public.dtb_csv USING btree (creator_id);


--
-- Name: idx_f55f48c3e8507796; Type: INDEX; Schema: public; Owner: dbuser
--

CREATE INDEX idx_f55f48c3e8507796 ON public.dtb_csv USING btree (csv_type_id);


--
-- Name: idx_fc3c24f09395c3f3; Type: INDEX; Schema: public; Owner: dbuser
--

CREATE INDEX idx_fc3c24f09395c3f3 ON public.dtb_cart USING btree (customer_id);


--
-- Name: secret_key; Type: INDEX; Schema: public; Owner: dbuser
--

CREATE UNIQUE INDEX secret_key ON public.dtb_customer USING btree (secret_key);


--
-- Name: dtb_member fk_10bc3be661220ea6; Type: FK CONSTRAINT; Schema: public; Owner: dbuser
--

ALTER TABLE ONLY public.dtb_member
    ADD CONSTRAINT fk_10bc3be661220ea6 FOREIGN KEY (creator_id) REFERENCES public.dtb_member(id);


--
-- Name: dtb_member fk_10bc3be681ec865b; Type: FK CONSTRAINT; Schema: public; Owner: dbuser
--

ALTER TABLE ONLY public.dtb_member
    ADD CONSTRAINT fk_10bc3be681ec865b FOREIGN KEY (authority_id) REFERENCES public.mtb_authority(id);


--
-- Name: dtb_member fk_10bc3be6bb3453db; Type: FK CONSTRAINT; Schema: public; Owner: dbuser
--

ALTER TABLE ONLY public.dtb_member
    ADD CONSTRAINT fk_10bc3be6bb3453db FOREIGN KEY (work_id) REFERENCES public.mtb_work(id);


--
-- Name: dtb_class_name fk_187c95ad61220ea6; Type: FK CONSTRAINT; Schema: public; Owner: dbuser
--

ALTER TABLE ONLY public.dtb_class_name
    ADD CONSTRAINT fk_187c95ad61220ea6 FOREIGN KEY (creator_id) REFERENCES public.dtb_member(id);


--
-- Name: dtb_product_class fk_1a11d1ba248d128; Type: FK CONSTRAINT; Schema: public; Owner: dbuser
--

ALTER TABLE ONLY public.dtb_product_class
    ADD CONSTRAINT fk_1a11d1ba248d128 FOREIGN KEY (class_category_id1) REFERENCES public.dtb_class_category(id);


--
-- Name: dtb_product_class fk_1a11d1ba4584665a; Type: FK CONSTRAINT; Schema: public; Owner: dbuser
--

ALTER TABLE ONLY public.dtb_product_class
    ADD CONSTRAINT fk_1a11d1ba4584665a FOREIGN KEY (product_id) REFERENCES public.dtb_product(id);


--
-- Name: dtb_product_class fk_1a11d1ba61220ea6; Type: FK CONSTRAINT; Schema: public; Owner: dbuser
--

ALTER TABLE ONLY public.dtb_product_class
    ADD CONSTRAINT fk_1a11d1ba61220ea6 FOREIGN KEY (creator_id) REFERENCES public.dtb_member(id);


--
-- Name: dtb_product_class fk_1a11d1ba9b418092; Type: FK CONSTRAINT; Schema: public; Owner: dbuser
--

ALTER TABLE ONLY public.dtb_product_class
    ADD CONSTRAINT fk_1a11d1ba9b418092 FOREIGN KEY (class_category_id2) REFERENCES public.dtb_class_category(id);


--
-- Name: dtb_product_class fk_1a11d1bab0524e01; Type: FK CONSTRAINT; Schema: public; Owner: dbuser
--

ALTER TABLE ONLY public.dtb_product_class
    ADD CONSTRAINT fk_1a11d1bab0524e01 FOREIGN KEY (sale_type_id) REFERENCES public.mtb_sale_type(id);


--
-- Name: dtb_product_class fk_1a11d1baba4269e; Type: FK CONSTRAINT; Schema: public; Owner: dbuser
--

ALTER TABLE ONLY public.dtb_product_class
    ADD CONSTRAINT fk_1a11d1baba4269e FOREIGN KEY (delivery_duration_id) REFERENCES public.dtb_delivery_duration(id);


--
-- Name: dtb_mail_template fk_1cb16db261220ea6; Type: FK CONSTRAINT; Schema: public; Owner: dbuser
--

ALTER TABLE ONLY public.dtb_mail_template
    ADD CONSTRAINT fk_1cb16db261220ea6 FOREIGN KEY (creator_id) REFERENCES public.dtb_member(id);


--
-- Name: dtb_base_info fk_1d3655f4e171ef5f; Type: FK CONSTRAINT; Schema: public; Owner: dbuser
--

ALTER TABLE ONLY public.dtb_base_info
    ADD CONSTRAINT fk_1d3655f4e171ef5f FOREIGN KEY (pref_id) REFERENCES public.mtb_pref(id);


--
-- Name: dtb_base_info fk_1d3655f4f92f3e70; Type: FK CONSTRAINT; Schema: public; Owner: dbuser
--

ALTER TABLE ONLY public.dtb_base_info
    ADD CONSTRAINT fk_1d3655f4f92f3e70 FOREIGN KEY (country_id) REFERENCES public.mtb_country(id);


--
-- Name: dtb_order fk_1d66d8074c3a3bb; Type: FK CONSTRAINT; Schema: public; Owner: dbuser
--

ALTER TABLE ONLY public.dtb_order
    ADD CONSTRAINT fk_1d66d8074c3a3bb FOREIGN KEY (payment_id) REFERENCES public.dtb_payment(id);


--
-- Name: dtb_order fk_1d66d8074ffa550e; Type: FK CONSTRAINT; Schema: public; Owner: dbuser
--

ALTER TABLE ONLY public.dtb_order
    ADD CONSTRAINT fk_1d66d8074ffa550e FOREIGN KEY (device_type_id) REFERENCES public.mtb_device_type(id);


--
-- Name: dtb_order fk_1d66d8075a2db2a0; Type: FK CONSTRAINT; Schema: public; Owner: dbuser
--

ALTER TABLE ONLY public.dtb_order
    ADD CONSTRAINT fk_1d66d8075a2db2a0 FOREIGN KEY (sex_id) REFERENCES public.mtb_sex(id);


--
-- Name: dtb_order fk_1d66d8079395c3f3; Type: FK CONSTRAINT; Schema: public; Owner: dbuser
--

ALTER TABLE ONLY public.dtb_order
    ADD CONSTRAINT fk_1d66d8079395c3f3 FOREIGN KEY (customer_id) REFERENCES public.dtb_customer(id);


--
-- Name: dtb_order fk_1d66d807be04ea9; Type: FK CONSTRAINT; Schema: public; Owner: dbuser
--

ALTER TABLE ONLY public.dtb_order
    ADD CONSTRAINT fk_1d66d807be04ea9 FOREIGN KEY (job_id) REFERENCES public.mtb_job(id);


--
-- Name: dtb_order fk_1d66d807e171ef5f; Type: FK CONSTRAINT; Schema: public; Owner: dbuser
--

ALTER TABLE ONLY public.dtb_order
    ADD CONSTRAINT fk_1d66d807e171ef5f FOREIGN KEY (pref_id) REFERENCES public.mtb_pref(id);


--
-- Name: dtb_order fk_1d66d807f92f3e70; Type: FK CONSTRAINT; Schema: public; Owner: dbuser
--

ALTER TABLE ONLY public.dtb_order
    ADD CONSTRAINT fk_1d66d807f92f3e70 FOREIGN KEY (country_id) REFERENCES public.mtb_country(id);


--
-- Name: dtb_shipping fk_2ebd22ce12136921; Type: FK CONSTRAINT; Schema: public; Owner: dbuser
--

ALTER TABLE ONLY public.dtb_shipping
    ADD CONSTRAINT fk_2ebd22ce12136921 FOREIGN KEY (delivery_id) REFERENCES public.dtb_delivery(id);


--
-- Name: dtb_shipping fk_2ebd22ce61220ea6; Type: FK CONSTRAINT; Schema: public; Owner: dbuser
--

ALTER TABLE ONLY public.dtb_shipping
    ADD CONSTRAINT fk_2ebd22ce61220ea6 FOREIGN KEY (creator_id) REFERENCES public.dtb_member(id);


--
-- Name: dtb_shipping fk_2ebd22ce8d9f6d38; Type: FK CONSTRAINT; Schema: public; Owner: dbuser
--

ALTER TABLE ONLY public.dtb_shipping
    ADD CONSTRAINT fk_2ebd22ce8d9f6d38 FOREIGN KEY (order_id) REFERENCES public.dtb_order(id);


--
-- Name: dtb_shipping fk_2ebd22cee171ef5f; Type: FK CONSTRAINT; Schema: public; Owner: dbuser
--

ALTER TABLE ONLY public.dtb_shipping
    ADD CONSTRAINT fk_2ebd22cee171ef5f FOREIGN KEY (pref_id) REFERENCES public.mtb_pref(id);


--
-- Name: dtb_shipping fk_2ebd22cef92f3e70; Type: FK CONSTRAINT; Schema: public; Owner: dbuser
--

ALTER TABLE ONLY public.dtb_shipping
    ADD CONSTRAINT fk_2ebd22cef92f3e70 FOREIGN KEY (country_id) REFERENCES public.mtb_country(id);


--
-- Name: dtb_product_image fk_3267cc7a4584665a; Type: FK CONSTRAINT; Schema: public; Owner: dbuser
--

ALTER TABLE ONLY public.dtb_product_image
    ADD CONSTRAINT fk_3267cc7a4584665a FOREIGN KEY (product_id) REFERENCES public.dtb_product(id);


--
-- Name: dtb_product_image fk_3267cc7a61220ea6; Type: FK CONSTRAINT; Schema: public; Owner: dbuser
--

ALTER TABLE ONLY public.dtb_product_image
    ADD CONSTRAINT fk_3267cc7a61220ea6 FOREIGN KEY (creator_id) REFERENCES public.dtb_member(id);


--
-- Name: dtb_delivery fk_3420d9fa61220ea6; Type: FK CONSTRAINT; Schema: public; Owner: dbuser
--

ALTER TABLE ONLY public.dtb_delivery
    ADD CONSTRAINT fk_3420d9fa61220ea6 FOREIGN KEY (creator_id) REFERENCES public.dtb_member(id);


--
-- Name: dtb_delivery fk_3420d9fab0524e01; Type: FK CONSTRAINT; Schema: public; Owner: dbuser
--

ALTER TABLE ONLY public.dtb_delivery
    ADD CONSTRAINT fk_3420d9fab0524e01 FOREIGN KEY (sale_type_id) REFERENCES public.mtb_sale_type(id);


--
-- Name: dtb_block_position fk_35dcd7318c22aa1a; Type: FK CONSTRAINT; Schema: public; Owner: dbuser
--

ALTER TABLE ONLY public.dtb_block_position
    ADD CONSTRAINT fk_35dcd7318c22aa1a FOREIGN KEY (layout_id) REFERENCES public.dtb_layout(id);


--
-- Name: dtb_block_position fk_35dcd731e9ed820c; Type: FK CONSTRAINT; Schema: public; Owner: dbuser
--

ALTER TABLE ONLY public.dtb_block_position
    ADD CONSTRAINT fk_35dcd731e9ed820c FOREIGN KEY (block_id) REFERENCES public.dtb_block(id);


--
-- Name: dtb_product_tag fk_4433e7214584665a; Type: FK CONSTRAINT; Schema: public; Owner: dbuser
--

ALTER TABLE ONLY public.dtb_product_tag
    ADD CONSTRAINT fk_4433e7214584665a FOREIGN KEY (product_id) REFERENCES public.dtb_product(id);


--
-- Name: dtb_product_tag fk_4433e72161220ea6; Type: FK CONSTRAINT; Schema: public; Owner: dbuser
--

ALTER TABLE ONLY public.dtb_product_tag
    ADD CONSTRAINT fk_4433e72161220ea6 FOREIGN KEY (creator_id) REFERENCES public.dtb_member(id);


--
-- Name: dtb_product_tag fk_4433e721bad26311; Type: FK CONSTRAINT; Schema: public; Owner: dbuser
--

ALTER TABLE ONLY public.dtb_product_tag
    ADD CONSTRAINT fk_4433e721bad26311 FOREIGN KEY (tag_id) REFERENCES public.dtb_tag(id);


--
-- Name: dtb_mail_history fk_4870ab1161220ea6; Type: FK CONSTRAINT; Schema: public; Owner: dbuser
--

ALTER TABLE ONLY public.dtb_mail_history
    ADD CONSTRAINT fk_4870ab1161220ea6 FOREIGN KEY (creator_id) REFERENCES public.dtb_member(id);


--
-- Name: dtb_mail_history fk_4870ab118d9f6d38; Type: FK CONSTRAINT; Schema: public; Owner: dbuser
--

ALTER TABLE ONLY public.dtb_mail_history
    ADD CONSTRAINT fk_4870ab118d9f6d38 FOREIGN KEY (order_id) REFERENCES public.dtb_order(id);


--
-- Name: dtb_delivery_fee fk_491552412136921; Type: FK CONSTRAINT; Schema: public; Owner: dbuser
--

ALTER TABLE ONLY public.dtb_delivery_fee
    ADD CONSTRAINT fk_491552412136921 FOREIGN KEY (delivery_id) REFERENCES public.dtb_delivery(id);


--
-- Name: dtb_delivery_fee fk_4915524e171ef5f; Type: FK CONSTRAINT; Schema: public; Owner: dbuser
--

ALTER TABLE ONLY public.dtb_delivery_fee
    ADD CONSTRAINT fk_4915524e171ef5f FOREIGN KEY (pref_id) REFERENCES public.mtb_pref(id);


--
-- Name: dtb_authority_role fk_4a1f70b161220ea6; Type: FK CONSTRAINT; Schema: public; Owner: dbuser
--

ALTER TABLE ONLY public.dtb_authority_role
    ADD CONSTRAINT fk_4a1f70b161220ea6 FOREIGN KEY (creator_id) REFERENCES public.dtb_member(id);


--
-- Name: dtb_authority_role fk_4a1f70b181ec865b; Type: FK CONSTRAINT; Schema: public; Owner: dbuser
--

ALTER TABLE ONLY public.dtb_authority_role
    ADD CONSTRAINT fk_4a1f70b181ec865b FOREIGN KEY (authority_id) REFERENCES public.mtb_authority(id);


--
-- Name: dtb_payment_option fk_5631540d12136921; Type: FK CONSTRAINT; Schema: public; Owner: dbuser
--

ALTER TABLE ONLY public.dtb_payment_option
    ADD CONSTRAINT fk_5631540d12136921 FOREIGN KEY (delivery_id) REFERENCES public.dtb_delivery(id);


--
-- Name: dtb_payment_option fk_5631540d4c3a3bb; Type: FK CONSTRAINT; Schema: public; Owner: dbuser
--

ALTER TABLE ONLY public.dtb_payment_option
    ADD CONSTRAINT fk_5631540d4c3a3bb FOREIGN KEY (payment_id) REFERENCES public.dtb_payment(id);


--
-- Name: dtb_tax_rule fk_59f696de1bd5c574; Type: FK CONSTRAINT; Schema: public; Owner: dbuser
--

ALTER TABLE ONLY public.dtb_tax_rule
    ADD CONSTRAINT fk_59f696de1bd5c574 FOREIGN KEY (rounding_type_id) REFERENCES public.mtb_rounding_type(id);


--
-- Name: dtb_tax_rule fk_59f696de21b06187; Type: FK CONSTRAINT; Schema: public; Owner: dbuser
--

ALTER TABLE ONLY public.dtb_tax_rule
    ADD CONSTRAINT fk_59f696de21b06187 FOREIGN KEY (product_class_id) REFERENCES public.dtb_product_class(id);


--
-- Name: dtb_tax_rule fk_59f696de4584665a; Type: FK CONSTRAINT; Schema: public; Owner: dbuser
--

ALTER TABLE ONLY public.dtb_tax_rule
    ADD CONSTRAINT fk_59f696de4584665a FOREIGN KEY (product_id) REFERENCES public.dtb_product(id);


--
-- Name: dtb_tax_rule fk_59f696de61220ea6; Type: FK CONSTRAINT; Schema: public; Owner: dbuser
--

ALTER TABLE ONLY public.dtb_tax_rule
    ADD CONSTRAINT fk_59f696de61220ea6 FOREIGN KEY (creator_id) REFERENCES public.dtb_member(id);


--
-- Name: dtb_tax_rule fk_59f696dee171ef5f; Type: FK CONSTRAINT; Schema: public; Owner: dbuser
--

ALTER TABLE ONLY public.dtb_tax_rule
    ADD CONSTRAINT fk_59f696dee171ef5f FOREIGN KEY (pref_id) REFERENCES public.mtb_pref(id);


--
-- Name: dtb_tax_rule fk_59f696def92f3e70; Type: FK CONSTRAINT; Schema: public; Owner: dbuser
--

ALTER TABLE ONLY public.dtb_tax_rule
    ADD CONSTRAINT fk_59f696def92f3e70 FOREIGN KEY (country_id) REFERENCES public.mtb_country(id);


--
-- Name: dtb_layout fk_5a62aa7c4ffa550e; Type: FK CONSTRAINT; Schema: public; Owner: dbuser
--

ALTER TABLE ONLY public.dtb_layout
    ADD CONSTRAINT fk_5a62aa7c4ffa550e FOREIGN KEY (device_type_id) REFERENCES public.mtb_device_type(id);


--
-- Name: dtb_category fk_5ed2c2b61220ea6; Type: FK CONSTRAINT; Schema: public; Owner: dbuser
--

ALTER TABLE ONLY public.dtb_category
    ADD CONSTRAINT fk_5ed2c2b61220ea6 FOREIGN KEY (creator_id) REFERENCES public.dtb_member(id);


--
-- Name: dtb_category fk_5ed2c2b796a8f92; Type: FK CONSTRAINT; Schema: public; Owner: dbuser
--

ALTER TABLE ONLY public.dtb_category
    ADD CONSTRAINT fk_5ed2c2b796a8f92 FOREIGN KEY (parent_category_id) REFERENCES public.dtb_category(id);


--
-- Name: dtb_login_history fk_6191dd4f7597d3fe; Type: FK CONSTRAINT; Schema: public; Owner: dbuser
--

ALTER TABLE ONLY public.dtb_login_history
    ADD CONSTRAINT fk_6191dd4f7597d3fe FOREIGN KEY (member_id) REFERENCES public.dtb_member(id) ON DELETE SET NULL;


--
-- Name: dtb_login_history fk_6191dd4f9fa62fdd; Type: FK CONSTRAINT; Schema: public; Owner: dbuser
--

ALTER TABLE ONLY public.dtb_login_history
    ADD CONSTRAINT fk_6191dd4f9fa62fdd FOREIGN KEY (login_history_status_id) REFERENCES public.mtb_login_history_status(id);


--
-- Name: dtb_block fk_6b54dcbd4ffa550e; Type: FK CONSTRAINT; Schema: public; Owner: dbuser
--

ALTER TABLE ONLY public.dtb_block
    ADD CONSTRAINT fk_6b54dcbd4ffa550e FOREIGN KEY (device_type_id) REFERENCES public.mtb_device_type(id);


--
-- Name: dtb_customer_address fk_6c38c0f89395c3f3; Type: FK CONSTRAINT; Schema: public; Owner: dbuser
--

ALTER TABLE ONLY public.dtb_customer_address
    ADD CONSTRAINT fk_6c38c0f89395c3f3 FOREIGN KEY (customer_id) REFERENCES public.dtb_customer(id);


--
-- Name: dtb_customer_address fk_6c38c0f8e171ef5f; Type: FK CONSTRAINT; Schema: public; Owner: dbuser
--

ALTER TABLE ONLY public.dtb_customer_address
    ADD CONSTRAINT fk_6c38c0f8e171ef5f FOREIGN KEY (pref_id) REFERENCES public.mtb_pref(id);


--
-- Name: dtb_customer_address fk_6c38c0f8f92f3e70; Type: FK CONSTRAINT; Schema: public; Owner: dbuser
--

ALTER TABLE ONLY public.dtb_customer_address
    ADD CONSTRAINT fk_6c38c0f8f92f3e70 FOREIGN KEY (country_id) REFERENCES public.mtb_country(id);


--
-- Name: dtb_payment fk_7aff628f61220ea6; Type: FK CONSTRAINT; Schema: public; Owner: dbuser
--

ALTER TABLE ONLY public.dtb_payment
    ADD CONSTRAINT fk_7aff628f61220ea6 FOREIGN KEY (creator_id) REFERENCES public.dtb_member(id);


--
-- Name: dtb_customer fk_8298bbe35a2db2a0; Type: FK CONSTRAINT; Schema: public; Owner: dbuser
--

ALTER TABLE ONLY public.dtb_customer
    ADD CONSTRAINT fk_8298bbe35a2db2a0 FOREIGN KEY (sex_id) REFERENCES public.mtb_sex(id);


--
-- Name: dtb_customer fk_8298bbe3be04ea9; Type: FK CONSTRAINT; Schema: public; Owner: dbuser
--

ALTER TABLE ONLY public.dtb_customer
    ADD CONSTRAINT fk_8298bbe3be04ea9 FOREIGN KEY (job_id) REFERENCES public.mtb_job(id);


--
-- Name: dtb_customer fk_8298bbe3c00af8a7; Type: FK CONSTRAINT; Schema: public; Owner: dbuser
--

ALTER TABLE ONLY public.dtb_customer
    ADD CONSTRAINT fk_8298bbe3c00af8a7 FOREIGN KEY (customer_status_id) REFERENCES public.mtb_customer_status(id);


--
-- Name: dtb_customer fk_8298bbe3e171ef5f; Type: FK CONSTRAINT; Schema: public; Owner: dbuser
--

ALTER TABLE ONLY public.dtb_customer
    ADD CONSTRAINT fk_8298bbe3e171ef5f FOREIGN KEY (pref_id) REFERENCES public.mtb_pref(id);


--
-- Name: dtb_customer fk_8298bbe3f92f3e70; Type: FK CONSTRAINT; Schema: public; Owner: dbuser
--

ALTER TABLE ONLY public.dtb_customer
    ADD CONSTRAINT fk_8298bbe3f92f3e70 FOREIGN KEY (country_id) REFERENCES public.mtb_country(id);


--
-- Name: dtb_template fk_94c12a694ffa550e; Type: FK CONSTRAINT; Schema: public; Owner: dbuser
--

ALTER TABLE ONLY public.dtb_template
    ADD CONSTRAINT fk_94c12a694ffa550e FOREIGN KEY (device_type_id) REFERENCES public.mtb_device_type(id);


--
-- Name: dtb_class_category fk_9b0d1dba61220ea6; Type: FK CONSTRAINT; Schema: public; Owner: dbuser
--

ALTER TABLE ONLY public.dtb_class_category
    ADD CONSTRAINT fk_9b0d1dba61220ea6 FOREIGN KEY (creator_id) REFERENCES public.dtb_member(id);


--
-- Name: dtb_class_category fk_9b0d1dbab462fb2a; Type: FK CONSTRAINT; Schema: public; Owner: dbuser
--

ALTER TABLE ONLY public.dtb_class_category
    ADD CONSTRAINT fk_9b0d1dbab462fb2a FOREIGN KEY (class_name_id) REFERENCES public.dtb_class_name(id);


--
-- Name: dtb_order_item fk_a0c8c3ed1bd5c574; Type: FK CONSTRAINT; Schema: public; Owner: dbuser
--

ALTER TABLE ONLY public.dtb_order_item
    ADD CONSTRAINT fk_a0c8c3ed1bd5c574 FOREIGN KEY (rounding_type_id) REFERENCES public.mtb_rounding_type(id);


--
-- Name: dtb_order_item fk_a0c8c3ed21b06187; Type: FK CONSTRAINT; Schema: public; Owner: dbuser
--

ALTER TABLE ONLY public.dtb_order_item
    ADD CONSTRAINT fk_a0c8c3ed21b06187 FOREIGN KEY (product_class_id) REFERENCES public.dtb_product_class(id);


--
-- Name: dtb_order_item fk_a0c8c3ed4584665a; Type: FK CONSTRAINT; Schema: public; Owner: dbuser
--

ALTER TABLE ONLY public.dtb_order_item
    ADD CONSTRAINT fk_a0c8c3ed4584665a FOREIGN KEY (product_id) REFERENCES public.dtb_product(id);


--
-- Name: dtb_order_item fk_a0c8c3ed4887f3f8; Type: FK CONSTRAINT; Schema: public; Owner: dbuser
--

ALTER TABLE ONLY public.dtb_order_item
    ADD CONSTRAINT fk_a0c8c3ed4887f3f8 FOREIGN KEY (shipping_id) REFERENCES public.dtb_shipping(id);


--
-- Name: dtb_order_item fk_a0c8c3ed84042c99; Type: FK CONSTRAINT; Schema: public; Owner: dbuser
--

ALTER TABLE ONLY public.dtb_order_item
    ADD CONSTRAINT fk_a0c8c3ed84042c99 FOREIGN KEY (tax_type_id) REFERENCES public.mtb_tax_type(id);


--
-- Name: dtb_order_item fk_a0c8c3ed8d9f6d38; Type: FK CONSTRAINT; Schema: public; Owner: dbuser
--

ALTER TABLE ONLY public.dtb_order_item
    ADD CONSTRAINT fk_a0c8c3ed8d9f6d38 FOREIGN KEY (order_id) REFERENCES public.dtb_order(id);


--
-- Name: dtb_order_item fk_a0c8c3eda2505856; Type: FK CONSTRAINT; Schema: public; Owner: dbuser
--

ALTER TABLE ONLY public.dtb_order_item
    ADD CONSTRAINT fk_a0c8c3eda2505856 FOREIGN KEY (tax_display_type_id) REFERENCES public.mtb_tax_display_type(id);


--
-- Name: dtb_order_item fk_a0c8c3edcad13ead; Type: FK CONSTRAINT; Schema: public; Owner: dbuser
--

ALTER TABLE ONLY public.dtb_order_item
    ADD CONSTRAINT fk_a0c8c3edcad13ead FOREIGN KEY (order_item_type_id) REFERENCES public.mtb_order_item_type(id);


--
-- Name: dtb_cart_item fk_b0228f741ad5cdbf; Type: FK CONSTRAINT; Schema: public; Owner: dbuser
--

ALTER TABLE ONLY public.dtb_cart_item
    ADD CONSTRAINT fk_b0228f741ad5cdbf FOREIGN KEY (cart_id) REFERENCES public.dtb_cart(id) ON DELETE CASCADE;


--
-- Name: dtb_cart_item fk_b0228f7421b06187; Type: FK CONSTRAINT; Schema: public; Owner: dbuser
--

ALTER TABLE ONLY public.dtb_cart_item
    ADD CONSTRAINT fk_b0228f7421b06187 FOREIGN KEY (product_class_id) REFERENCES public.dtb_product_class(id);


--
-- Name: dtb_product_category fk_b057789112469de2; Type: FK CONSTRAINT; Schema: public; Owner: dbuser
--

ALTER TABLE ONLY public.dtb_product_category
    ADD CONSTRAINT fk_b057789112469de2 FOREIGN KEY (category_id) REFERENCES public.dtb_category(id);


--
-- Name: dtb_product_category fk_b05778914584665a; Type: FK CONSTRAINT; Schema: public; Owner: dbuser
--

ALTER TABLE ONLY public.dtb_product_category
    ADD CONSTRAINT fk_b05778914584665a FOREIGN KEY (product_id) REFERENCES public.dtb_product(id);


--
-- Name: dtb_product_stock fk_bc6c9e4521b06187; Type: FK CONSTRAINT; Schema: public; Owner: dbuser
--

ALTER TABLE ONLY public.dtb_product_stock
    ADD CONSTRAINT fk_bc6c9e4521b06187 FOREIGN KEY (product_class_id) REFERENCES public.dtb_product_class(id);


--
-- Name: dtb_product_stock fk_bc6c9e4561220ea6; Type: FK CONSTRAINT; Schema: public; Owner: dbuser
--

ALTER TABLE ONLY public.dtb_product_stock
    ADD CONSTRAINT fk_bc6c9e4561220ea6 FOREIGN KEY (creator_id) REFERENCES public.dtb_member(id);


--
-- Name: dtb_product fk_c49de22f557b630; Type: FK CONSTRAINT; Schema: public; Owner: dbuser
--

ALTER TABLE ONLY public.dtb_product
    ADD CONSTRAINT fk_c49de22f557b630 FOREIGN KEY (product_status_id) REFERENCES public.mtb_product_status(id);


--
-- Name: dtb_product fk_c49de22f61220ea6; Type: FK CONSTRAINT; Schema: public; Owner: dbuser
--

ALTER TABLE ONLY public.dtb_product
    ADD CONSTRAINT fk_c49de22f61220ea6 FOREIGN KEY (creator_id) REFERENCES public.dtb_member(id);


--
-- Name: dtb_page fk_e3951a67d0618e8c; Type: FK CONSTRAINT; Schema: public; Owner: dbuser
--

ALTER TABLE ONLY public.dtb_page
    ADD CONSTRAINT fk_e3951a67d0618e8c FOREIGN KEY (master_page_id) REFERENCES public.dtb_page(id);


--
-- Name: dtb_delivery_time fk_e80ee3a612136921; Type: FK CONSTRAINT; Schema: public; Owner: dbuser
--

ALTER TABLE ONLY public.dtb_delivery_time
    ADD CONSTRAINT fk_e80ee3a612136921 FOREIGN KEY (delivery_id) REFERENCES public.dtb_delivery(id);


--
-- Name: dtb_news fk_ea4c351761220ea6; Type: FK CONSTRAINT; Schema: public; Owner: dbuser
--

ALTER TABLE ONLY public.dtb_news
    ADD CONSTRAINT fk_ea4c351761220ea6 FOREIGN KEY (creator_id) REFERENCES public.dtb_member(id);


--
-- Name: dtb_customer_favorite_product fk_ed6313834584665a; Type: FK CONSTRAINT; Schema: public; Owner: dbuser
--

ALTER TABLE ONLY public.dtb_customer_favorite_product
    ADD CONSTRAINT fk_ed6313834584665a FOREIGN KEY (product_id) REFERENCES public.dtb_product(id);


--
-- Name: dtb_customer_favorite_product fk_ed6313839395c3f3; Type: FK CONSTRAINT; Schema: public; Owner: dbuser
--

ALTER TABLE ONLY public.dtb_customer_favorite_product
    ADD CONSTRAINT fk_ed6313839395c3f3 FOREIGN KEY (customer_id) REFERENCES public.dtb_customer(id);


--
-- Name: dtb_page_layout fk_f27999418c22aa1a; Type: FK CONSTRAINT; Schema: public; Owner: dbuser
--

ALTER TABLE ONLY public.dtb_page_layout
    ADD CONSTRAINT fk_f27999418c22aa1a FOREIGN KEY (layout_id) REFERENCES public.dtb_layout(id);


--
-- Name: dtb_page_layout fk_f2799941c4663e4; Type: FK CONSTRAINT; Schema: public; Owner: dbuser
--

ALTER TABLE ONLY public.dtb_page_layout
    ADD CONSTRAINT fk_f2799941c4663e4 FOREIGN KEY (page_id) REFERENCES public.dtb_page(id);


--
-- Name: dtb_csv fk_f55f48c361220ea6; Type: FK CONSTRAINT; Schema: public; Owner: dbuser
--

ALTER TABLE ONLY public.dtb_csv
    ADD CONSTRAINT fk_f55f48c361220ea6 FOREIGN KEY (creator_id) REFERENCES public.dtb_member(id);


--
-- Name: dtb_csv fk_f55f48c3e8507796; Type: FK CONSTRAINT; Schema: public; Owner: dbuser
--

ALTER TABLE ONLY public.dtb_csv
    ADD CONSTRAINT fk_f55f48c3e8507796 FOREIGN KEY (csv_type_id) REFERENCES public.mtb_csv_type(id);


--
-- Name: dtb_cart fk_fc3c24f09395c3f3; Type: FK CONSTRAINT; Schema: public; Owner: dbuser
--

ALTER TABLE ONLY public.dtb_cart
    ADD CONSTRAINT fk_fc3c24f09395c3f3 FOREIGN KEY (customer_id) REFERENCES public.dtb_customer(id);


--
-- PostgreSQL database dump complete
--

