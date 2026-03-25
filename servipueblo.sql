--
-- PostgreSQL database dump
--

\restrict lfzZRcpJlZ4cxZHMPU2fnieoZ4PsnFJTaXSFkjbTjgXNAbqEY72heeKqmL3yq0C

-- Dumped from database version 16.12
-- Dumped by pg_dump version 18.3

SET statement_timeout = 0;
SET lock_timeout = 0;
SET idle_in_transaction_session_timeout = 0;
SET transaction_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SELECT pg_catalog.set_config('search_path', '', false);
SET check_function_bodies = false;
SET xmloption = content;
SET client_min_messages = warning;
SET row_security = off;

SET default_tablespace = '';

SET default_table_access_method = heap;

--
-- Name: categories; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.categories (
    id bigint NOT NULL,
    name character varying(80) NOT NULL,
    slug character varying(80) NOT NULL,
    icon character varying(60),
    sort_order smallint DEFAULT '0'::smallint NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


--
-- Name: categories_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.categories_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: categories_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.categories_id_seq OWNED BY public.categories.id;


--
-- Name: category_worker; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.category_worker (
    worker_id bigint NOT NULL,
    category_id bigint NOT NULL,
    is_primary boolean DEFAULT false NOT NULL
);


--
-- Name: email_verifications; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.email_verifications (
    id bigint NOT NULL,
    email character varying(150) NOT NULL,
    code character varying(6) NOT NULL,
    expires_at timestamp(0) without time zone NOT NULL,
    used boolean DEFAULT false NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


--
-- Name: email_verifications_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.email_verifications_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: email_verifications_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.email_verifications_id_seq OWNED BY public.email_verifications.id;


--
-- Name: failed_jobs; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.failed_jobs (
    id bigint NOT NULL,
    uuid character varying(255) NOT NULL,
    connection text NOT NULL,
    queue text NOT NULL,
    payload text NOT NULL,
    exception text NOT NULL,
    failed_at timestamp(0) without time zone DEFAULT CURRENT_TIMESTAMP NOT NULL
);


--
-- Name: failed_jobs_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.failed_jobs_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: failed_jobs_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.failed_jobs_id_seq OWNED BY public.failed_jobs.id;


--
-- Name: migrations; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.migrations (
    id integer NOT NULL,
    migration character varying(255) NOT NULL,
    batch integer NOT NULL
);


--
-- Name: migrations_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.migrations_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: migrations_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.migrations_id_seq OWNED BY public.migrations.id;


--
-- Name: password_resets; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.password_resets (
    email character varying(255) NOT NULL,
    token character varying(255) NOT NULL,
    created_at timestamp(0) without time zone
);


--
-- Name: personal_access_tokens; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.personal_access_tokens (
    id bigint NOT NULL,
    tokenable_type character varying(255) NOT NULL,
    tokenable_id bigint NOT NULL,
    name character varying(255) NOT NULL,
    token character varying(64) NOT NULL,
    abilities text,
    last_used_at timestamp(0) without time zone,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


--
-- Name: personal_access_tokens_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.personal_access_tokens_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: personal_access_tokens_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.personal_access_tokens_id_seq OWNED BY public.personal_access_tokens.id;


--
-- Name: ratings; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.ratings (
    id bigint NOT NULL,
    worker_id bigint NOT NULL,
    score smallint NOT NULL,
    comment text,
    reviewer_name character varying(80),
    ip_address inet,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    CONSTRAINT ratings_score_check CHECK (((score >= 1) AND (score <= 5)))
);


--
-- Name: ratings_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.ratings_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: ratings_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.ratings_id_seq OWNED BY public.ratings.id;


--
-- Name: users; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.users (
    id bigint NOT NULL,
    name character varying(255) NOT NULL,
    email character varying(255) NOT NULL,
    email_verified_at timestamp(0) without time zone,
    password character varying(255),
    remember_token character varying(100),
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    google_id character varying(255),
    avatar character varying(255)
);


--
-- Name: users_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.users_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: users_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.users_id_seq OWNED BY public.users.id;


--
-- Name: worker_events; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.worker_events (
    id bigint NOT NULL,
    worker_id bigint NOT NULL,
    type character varying(20) NOT NULL,
    ip_hash character varying(64),
    referrer character varying(300),
    created_at timestamp(0) without time zone DEFAULT CURRENT_TIMESTAMP NOT NULL
);


--
-- Name: worker_events_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.worker_events_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: worker_events_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.worker_events_id_seq OWNED BY public.worker_events.id;


--
-- Name: worker_photos; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.worker_photos (
    id bigint NOT NULL,
    worker_id bigint NOT NULL,
    path character varying(255) NOT NULL,
    "order" smallint DEFAULT '0'::smallint NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


--
-- Name: worker_photos_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.worker_photos_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: worker_photos_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.worker_photos_id_seq OWNED BY public.worker_photos.id;


--
-- Name: workers; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.workers (
    id bigint NOT NULL,
    name character varying(120) NOT NULL,
    slug character varying(140) NOT NULL,
    description text,
    phone character varying(20) NOT NULL,
    town character varying(100) NOT NULL,
    photo_path character varying(255),
    average_rating numeric(3,2) DEFAULT '0'::numeric NOT NULL,
    ratings_count integer DEFAULT 0 NOT NULL,
    is_active boolean DEFAULT true NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    recommendations_count integer DEFAULT 0 NOT NULL,
    years_experience smallint,
    availability character varying(20) DEFAULT 'available'::character varying NOT NULL
);


--
-- Name: workers_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.workers_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: workers_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.workers_id_seq OWNED BY public.workers.id;


--
-- Name: categories id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.categories ALTER COLUMN id SET DEFAULT nextval('public.categories_id_seq'::regclass);


--
-- Name: email_verifications id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.email_verifications ALTER COLUMN id SET DEFAULT nextval('public.email_verifications_id_seq'::regclass);


--
-- Name: failed_jobs id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.failed_jobs ALTER COLUMN id SET DEFAULT nextval('public.failed_jobs_id_seq'::regclass);


--
-- Name: migrations id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.migrations ALTER COLUMN id SET DEFAULT nextval('public.migrations_id_seq'::regclass);


--
-- Name: personal_access_tokens id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.personal_access_tokens ALTER COLUMN id SET DEFAULT nextval('public.personal_access_tokens_id_seq'::regclass);


--
-- Name: ratings id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.ratings ALTER COLUMN id SET DEFAULT nextval('public.ratings_id_seq'::regclass);


--
-- Name: users id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.users ALTER COLUMN id SET DEFAULT nextval('public.users_id_seq'::regclass);


--
-- Name: worker_events id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.worker_events ALTER COLUMN id SET DEFAULT nextval('public.worker_events_id_seq'::regclass);


--
-- Name: worker_photos id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.worker_photos ALTER COLUMN id SET DEFAULT nextval('public.worker_photos_id_seq'::regclass);


--
-- Name: workers id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.workers ALTER COLUMN id SET DEFAULT nextval('public.workers_id_seq'::regclass);


--
-- Data for Name: categories; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.categories (id, name, slug, icon, sort_order, created_at, updated_at) FROM stdin;
1	Plomero	plomero	🔧	1	2026-03-16 18:45:24	2026-03-16 18:45:24
2	Electricista	electricista	⚡	2	2026-03-16 18:45:24	2026-03-16 18:45:24
3	Albañil	albanil	🧱	3	2026-03-16 18:45:24	2026-03-16 18:45:24
4	Carpintero	carpintero	🪚	4	2026-03-16 18:45:24	2026-03-16 18:45:24
5	Pintor	pintor	🖌️	5	2026-03-16 18:45:24	2026-03-16 18:45:24
6	Herrero	herrero	⚙️	6	2026-03-16 18:45:24	2026-03-16 18:45:24
7	Jardinero	jardinero	🌿	7	2026-03-16 18:45:24	2026-03-16 18:45:24
8	Mecánico	mecanico	🔩	8	2026-03-16 18:45:24	2026-03-16 18:45:24
9	Técnico en A/C	tecnico-en-ac	❄️	9	2026-03-16 18:45:24	2026-03-16 18:45:24
10	Técnico en TV	tecnico-en-tv	📺	10	2026-03-16 18:45:24	2026-03-16 18:45:24
11	Cerrajero	cerrajero	🔑	11	2026-03-16 18:45:24	2026-03-16 18:45:24
12	Limpieza	limpieza	🧹	12	2026-03-16 18:45:24	2026-03-16 18:45:24
13	Fumigación	fumigacion	🪲	13	2026-03-16 18:45:24	2026-03-16 18:45:24
14	Otro	otro	🛠️	99	2026-03-16 18:45:24	2026-03-16 18:45:24
\.


--
-- Data for Name: category_worker; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.category_worker (worker_id, category_id, is_primary) FROM stdin;
1	2	t
2	1	t
3	6	t
4	6	t
5	9	t
6	1	t
7	4	t
8	1	t
9	2	t
10	3	t
11	9	t
12	10	t
13	12	t
14	14	t
15	2	t
16	3	t
17	6	t
18	1	t
19	9	t
20	3	t
21	11	t
22	8	t
23	7	t
24	12	t
25	6	t
26	11	t
27	6	t
28	11	t
29	13	t
30	1	t
31	8	t
32	2	t
33	14	t
34	7	t
35	12	t
36	2	t
37	11	t
38	3	t
39	14	t
40	7	t
41	7	t
42	12	t
43	1	t
44	3	t
45	14	t
46	6	t
47	11	t
48	9	t
49	3	t
50	4	t
51	4	t
52	1	t
4	2	f
8	14	f
9	9	f
22	1	f
23	2	f
29	4	f
32	11	f
33	11	f
37	5	f
38	10	f
40	13	f
42	5	f
43	4	f
46	11	f
\.


--
-- Data for Name: email_verifications; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.email_verifications (id, email, code, expires_at, used, created_at, updated_at) FROM stdin;
1	diego14esp@hotmail.com	594703	2026-03-16 19:54:07	f	2026-03-16 19:44:07	2026-03-16 19:44:07
2	diego14esp@hotmail.com	095153	2026-03-16 20:02:58	t	2026-03-16 19:52:58	2026-03-16 19:53:59
\.


--
-- Data for Name: failed_jobs; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.failed_jobs (id, uuid, connection, queue, payload, exception, failed_at) FROM stdin;
\.


--
-- Data for Name: migrations; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.migrations (id, migration, batch) FROM stdin;
1	2014_10_12_000000_create_users_table	1
2	2014_10_12_100000_create_password_resets_table	1
3	2019_08_19_000000_create_failed_jobs_table	1
4	2019_12_14_000001_create_personal_access_tokens_table	1
5	2026_03_16_000001_create_categories_table	1
6	2026_03_16_000002_create_workers_table	1
7	2026_03_16_000003_create_ratings_table	1
8	2026_03_16_193206_create_email_verifications_table	2
9	2026_03_16_200235_add_google_id_to_users_table	3
10	2026_03_16_204152_create_worker_photos_table	4
11	2026_03_16_204204_add_recommendations_to_workers_table	4
12	2026_03_16_211802_create_worker_events_table	5
13	2026_03_25_000001_create_category_worker_table	6
14	2026_03_25_000002_migrate_worker_category_to_pivot	6
15	2026_03_25_184022_add_years_experience_to_workers_table	7
16	2026_03_25_185218_add_availability_to_workers_table	8
\.


--
-- Data for Name: password_resets; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.password_resets (email, token, created_at) FROM stdin;
\.


--
-- Data for Name: personal_access_tokens; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.personal_access_tokens (id, tokenable_type, tokenable_id, name, token, abilities, last_used_at, created_at, updated_at) FROM stdin;
\.


--
-- Data for Name: ratings; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.ratings (id, worker_id, score, comment, reviewer_name, ip_address, created_at, updated_at) FROM stdin;
1	1	5	Muy buen trabajo	Juan	127.0.0.1	2026-03-16 19:03:47	2026-03-16 19:03:47
2	1	4	Excelente servicio	Pablo	127.0.0.1	2026-03-16 19:59:05	2026-03-16 19:59:05
3	1	5	Muy recomendable	Diego Espíndola	127.0.0.1	2026-03-25 19:17:36	2026-03-25 19:17:36
\.


--
-- Data for Name: users; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.users (id, name, email, email_verified_at, password, remember_token, created_at, updated_at, google_id, avatar) FROM stdin;
1	Administrador	admin@servipueblo.com	\N	$2y$10$/Z8WIjyeNbyeHHj2fL09Yes4/KUQTKtewmFJ9cAYR7LLwCM1tUk7K	\N	2026-03-16 18:45:24	2026-03-16 18:45:24	\N	\N
2	Diego Espíndola	diegoesp63@gmail.com	\N	\N	feSvNBF9xYh8rVC8MvH8d0APDGwxlWCwETPcZy94TAojycDSFXJsGaVEOdSz	2026-03-25 19:17:18	2026-03-25 19:17:18	114166096392959107129	https://lh3.googleusercontent.com/a/ACg8ocLqmeyevsnRVXPvPRXUeplKQFMBXP0hM9wKJB2_9iWFYu4CLp7FYA=s96-c
3	Diego Espindola	diegoesp294@gmail.com	\N	\N	hPpbESGkQtZg68CB5WhkMMUVqNBsMkRlxLiMBbd9fQSIAMLGBuziwbZOJ1xL	2026-03-25 19:19:10	2026-03-25 19:19:10	112453139130264232069	https://lh3.googleusercontent.com/a/ACg8ocJPbWNP0TMaRYksocew-ChwH4Hp0i6W9CX8T1dCofq04AkBVw=s96-c
\.


--
-- Data for Name: worker_events; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.worker_events (id, worker_id, type, ip_hash, referrer, created_at) FROM stdin;
1	1	view	12ca17b49af2289436f303e0166030a21e525d266e209267433801a8fd4071a0	http://127.0.0.1:8000/trabajadores	2026-03-25 13:06:27
2	8	view	12ca17b49af2289436f303e0166030a21e525d266e209267433801a8fd4071a0	http://127.0.0.1:8000/	2026-03-25 15:35:51
3	43	view	12ca17b49af2289436f303e0166030a21e525d266e209267433801a8fd4071a0	http://127.0.0.1:8000/	2026-03-25 15:35:57
4	1	view	12ca17b49af2289436f303e0166030a21e525d266e209267433801a8fd4071a0		2026-03-25 18:34:43
5	32	view	12ca17b49af2289436f303e0166030a21e525d266e209267433801a8fd4071a0	http://127.0.0.1:8000/trabajadores?page=3	2026-03-25 19:00:32
6	17	view	12ca17b49af2289436f303e0166030a21e525d266e209267433801a8fd4071a0	http://127.0.0.1:8000/	2026-03-25 19:03:18
7	8	view	12ca17b49af2289436f303e0166030a21e525d266e209267433801a8fd4071a0	http://127.0.0.1:8000/	2026-03-25 19:18:15
8	29	view	12ca17b49af2289436f303e0166030a21e525d266e209267433801a8fd4071a0	http://127.0.0.1:8000/	2026-03-25 19:23:24
\.


--
-- Data for Name: worker_photos; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.worker_photos (id, worker_id, path, "order", created_at, updated_at) FROM stdin;
\.


--
-- Data for Name: workers; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.workers (id, name, slug, description, phone, town, photo_path, average_rating, ratings_count, is_active, created_at, updated_at, recommendations_count, years_experience, availability) FROM stdin;
1	Diego Espíndola	diego-espindola	Todos tipo de trabajo de plomería	3856452191	Los telares	\N	0.00	0	t	2026-03-16 19:00:57	2026-03-16 19:01:24	0	\N	available
2	Juan Garcia	juan-garcia	Plomero tiempo completo	3856452191	Los Telares	\N	0.00	0	t	2026-03-16 20:53:39	2026-03-16 20:58:19	0	\N	available
3	Oscar Álvarez	oscar-alvarez-2437	\N	+5493858970064	Frías	\N	3.61	23	t	2026-03-25 13:57:20	2026-03-25 13:57:20	15	\N	available
4	Ramón Espeche	ramon-espeche-5550	Atención rápida y presupuesto sin cargo. Disponible fines de semana.	+5493851991327	Frías	\N	3.86	21	t	2026-03-25 13:57:20	2026-03-25 13:57:20	15	\N	available
5	Pedro Gutiérrez	pedro-gutierrez-7319	Precio justo y materiales incluidos. Trabajo garantizado por escrito.	+5493858098511	Los Telares	\N	3.15	13	t	2026-03-25 13:57:20	2026-03-25 13:57:20	7	\N	available
6	María López	maria-lopez-6791	Experiencia en obras nuevas y reparaciones. Consulte por paquetes.	+5493851454989	Bandera	\N	4.29	17	t	2026-03-25 13:57:20	2026-03-25 13:57:20	14	\N	available
7	Ana González	ana-gonzalez-112	Más de 10 años de experiencia en la zona. Trabajo garantizado y materiales de primera calidad.	+5493852684296	Los Telares	\N	3.00	2	t	2026-03-25 13:57:20	2026-03-25 13:57:20	1	\N	available
8	Fabián Rojas	fabian-rojas-1421	\N	+5493851449220	Loreto	\N	4.62	21	t	2026-03-25 13:57:20	2026-03-25 13:57:20	19	\N	available
9	Héctor Mansilla	hector-mansilla-658	Experiencia en obras nuevas y reparaciones. Consulte por paquetes.	+5493858371087	Añatuya	\N	3.93	15	t	2026-03-25 13:57:20	2026-03-25 13:57:20	11	\N	available
10	Fernando Acosta	fernando-acosta-7821	Trabajo a domicilio en toda la zona. Presupuesto gratis.	+5493856137407	Los Telares	\N	4.79	19	t	2026-03-25 13:57:20	2026-03-25 13:57:20	18	\N	available
11	Fabián Rojas	fabian-rojas-7388	Técnico matriculado con años de trayectoria en la provincia.	+5493855173175	Los Telares	\N	2.78	9	t	2026-03-25 13:57:20	2026-03-25 13:57:20	4	\N	available
12	Mario Suárez	mario-suarez-2693	Especialista en trabajos de urgencia. Atiendo toda la región.	+5493855071530	Tintina	\N	5.00	6	t	2026-03-25 13:57:20	2026-03-25 13:57:20	6	\N	available
13	Roberto Paz	roberto-paz-8798	Trabajo a domicilio en toda la zona. Presupuesto gratis.	+5493859538447	Tintina	\N	4.37	19	t	2026-03-25 13:57:20	2026-03-25 13:57:20	16	\N	available
14	Norma Quiroga	norma-quiroga-533	Técnico matriculado con años de trayectoria en la provincia.	+5493854434091	Los Telares	\N	5.00	16	t	2026-03-25 13:57:20	2026-03-25 13:57:20	16	\N	available
15	Fernando Acosta	fernando-acosta-3591	Trabajo prolijo y puntual. Referencias disponibles. Llame sin compromiso.	+5493856945796	Icaño	\N	3.29	7	t	2026-03-25 13:57:20	2026-03-25 13:57:20	4	\N	available
16	Fernando Acosta	fernando-acosta-6453	Trabajo a domicilio en toda la zona. Presupuesto gratis.	+5493851454864	Los Telares	\N	3.09	23	t	2026-03-25 13:57:20	2026-03-25 13:57:20	12	\N	available
17	Fernando Acosta	fernando-acosta-4359	\N	+5493854957609	Frías	\N	4.79	19	t	2026-03-25 13:57:20	2026-03-25 13:57:20	18	\N	available
18	Silvia Bustamante	silvia-bustamante-3003	Especialista en trabajos de urgencia. Atiendo toda la región.	+5493859359988	Bandera	\N	4.73	15	t	2026-03-25 13:57:20	2026-03-25 13:57:20	14	\N	available
19	Roberto Paz	roberto-paz-1501	Experiencia en obras nuevas y reparaciones. Consulte por paquetes.	+5493858646527	Frías	\N	4.11	18	t	2026-03-25 13:57:20	2026-03-25 13:57:20	14	\N	available
20	Daniel Leiva	daniel-leiva-7366	\N	+5493856858795	Loreto	\N	5.00	18	t	2026-03-25 13:57:20	2026-03-25 13:57:20	18	\N	available
21	Jorge Figueroa	jorge-figueroa-4573	Precio justo y materiales incluidos. Trabajo garantizado por escrito.	+5493857267359	Quimilí	\N	5.00	25	t	2026-03-25 13:57:20	2026-03-25 13:57:20	25	\N	available
22	Fabián Rojas	fabian-rojas-2540	\N	+5493852416865	Icaño	\N	4.14	14	t	2026-03-25 13:57:20	2026-03-25 13:57:20	11	\N	available
23	Rosa Vargas	rosa-vargas-2197	Precio justo y materiales incluidos. Trabajo garantizado por escrito.	+5493855877385	Quimilí	\N	3.40	10	t	2026-03-25 13:57:20	2026-03-25 13:57:20	6	\N	available
24	Daniel Leiva	daniel-leiva-1828	\N	+5493855219994	Bandera	\N	4.20	15	t	2026-03-25 13:57:20	2026-03-25 13:57:20	12	\N	available
25	Oscar Álvarez	oscar-alvarez-6977	Precio justo y materiales incluidos. Trabajo garantizado por escrito.	+5493856235633	Tintina	\N	5.00	14	t	2026-03-25 13:57:20	2026-03-25 13:57:20	14	\N	available
26	María López	maria-lopez-6091	Experiencia en obras nuevas y reparaciones. Consulte por paquetes.	+5493850945643	Suncho Corral	\N	3.40	10	t	2026-03-25 13:57:20	2026-03-25 13:57:20	6	\N	available
27	Héctor Mansilla	hector-mansilla-2975	Especialista en trabajos de urgencia. Atiendo toda la región.	+5493856202884	Icaño	\N	3.00	18	t	2026-03-25 13:57:20	2026-03-25 13:57:20	9	\N	available
28	Patricia Medina	patricia-medina-7132	Especialista en trabajos de urgencia. Atiendo toda la región.	+5493858507023	Tintina	\N	3.53	19	t	2026-03-25 13:57:20	2026-03-25 13:57:20	12	\N	available
29	Héctor Mansilla	hector-mansilla-8685	\N	+5493853829498	Loreto	\N	4.62	21	t	2026-03-25 13:57:20	2026-03-25 13:57:20	19	\N	available
30	Jorge Figueroa	jorge-figueroa-560	Precio justo y materiales incluidos. Trabajo garantizado por escrito.	+5493855918631	Los Telares	\N	3.67	9	t	2026-03-25 13:57:20	2026-03-25 13:57:20	6	\N	available
31	Néstor Pereyra	nestor-pereyra-6129	Técnico matriculado con años de trayectoria en la provincia.	+5493857472788	Loreto	\N	4.45	22	t	2026-03-25 13:57:20	2026-03-25 13:57:20	19	\N	available
32	Claudia Ruiz	claudia-ruiz-7699	Trabajo prolijo y puntual. Referencias disponibles. Llame sin compromiso.	+5493853653238	Los Telares	\N	4.05	21	t	2026-03-25 13:57:20	2026-03-25 13:57:20	16	\N	available
33	Silvia Bustamante	silvia-bustamante-9196	\N	+5493858206458	Bandera	\N	0.00	0	t	2026-03-25 13:57:20	2026-03-25 13:57:20	0	\N	available
34	Norma Quiroga	norma-quiroga-3920	Técnico matriculado con años de trayectoria en la provincia.	+5493859690807	Los Telares	\N	2.85	13	t	2026-03-25 13:57:20	2026-03-25 13:57:20	6	\N	available
35	Silvia Bustamante	silvia-bustamante-2164	Experiencia en obras nuevas y reparaciones. Consulte por paquetes.	+5493855987773	Quimilí	\N	5.00	6	t	2026-03-25 13:57:20	2026-03-25 13:57:20	6	\N	available
36	José Coronel	jose-coronel-1270	Trabajo prolijo y puntual. Referencias disponibles. Llame sin compromiso.	+5493852357590	Tintina	\N	3.00	14	t	2026-03-25 13:57:20	2026-03-25 13:57:20	7	\N	available
37	Héctor Mansilla	hector-mansilla-7895	Más de 10 años de experiencia en la zona. Trabajo garantizado y materiales de primera calidad.	+5493852636090	Bandera	\N	4.50	16	t	2026-03-25 13:57:20	2026-03-25 13:57:20	14	\N	available
38	Oscar Álvarez	oscar-alvarez-1578	\N	+5493857843632	Suncho Corral	\N	4.00	8	t	2026-03-25 13:57:20	2026-03-25 13:57:20	6	\N	available
39	Héctor Mansilla	hector-mansilla-8977	Precio justo y materiales incluidos. Trabajo garantizado por escrito.	+5493855480531	Quimilí	\N	4.45	22	t	2026-03-25 13:57:20	2026-03-25 13:57:20	19	\N	available
40	Laura Soria	laura-soria-621	Más de 10 años de experiencia en la zona. Trabajo garantizado y materiales de primera calidad.	+5493853574569	Icaño	\N	4.50	8	t	2026-03-25 13:57:20	2026-03-25 13:57:20	7	\N	available
41	Sergio Luna	sergio-luna-9106	Atención rápida y presupuesto sin cargo. Disponible fines de semana.	+5493851374944	Añatuya	\N	3.00	8	t	2026-03-25 13:57:20	2026-03-25 13:57:20	4	\N	available
42	Roberto Paz	roberto-paz-5435	\N	+5493854154506	Quimilí	\N	3.00	8	t	2026-03-25 13:57:20	2026-03-25 13:57:20	4	\N	available
43	Carlos Juárez	carlos-juarez-8269	Experiencia en obras nuevas y reparaciones. Consulte por paquetes.	+5493856992566	Frías	\N	4.45	22	t	2026-03-25 13:57:20	2026-03-25 13:57:20	19	\N	available
44	Patricia Medina	patricia-medina-3495	Precio justo y materiales incluidos. Trabajo garantizado por escrito.	+5493856762579	Tintina	\N	2.71	7	t	2026-03-25 13:57:20	2026-03-25 13:57:20	3	\N	available
45	Rosa Vargas	rosa-vargas-7871	Precio justo y materiales incluidos. Trabajo garantizado por escrito.	+5493857658796	Icaño	\N	4.16	19	t	2026-03-25 13:57:20	2026-03-25 13:57:20	15	\N	available
46	Gustavo Ibáñez	gustavo-ibanez-84	Atención rápida y presupuesto sin cargo. Disponible fines de semana.	+5493851130218	Quimilí	\N	3.00	6	t	2026-03-25 13:57:20	2026-03-25 13:57:20	3	\N	available
47	Daniel Leiva	daniel-leiva-2987	Trabajo a domicilio en toda la zona. Presupuesto gratis.	+5493855878815	Los Telares	\N	4.00	4	t	2026-03-25 13:57:20	2026-03-25 13:57:20	3	\N	available
48	Miguel Díaz	miguel-diaz-9748	Técnico matriculado con años de trayectoria en la provincia.	+5493852269027	Frías	\N	3.53	19	t	2026-03-25 13:57:20	2026-03-25 13:57:20	12	\N	available
49	Norma Quiroga	norma-quiroga-6223	Trabajo a domicilio en toda la zona. Presupuesto gratis.	+5493851744006	Suncho Corral	\N	4.00	4	t	2026-03-25 13:57:20	2026-03-25 13:57:20	3	\N	available
50	Norma Quiroga	norma-quiroga-680	Experiencia en obras nuevas y reparaciones. Consulte por paquetes.	+5493859924147	Los Telares	\N	3.00	4	t	2026-03-25 13:57:20	2026-03-25 13:57:20	2	\N	available
51	Ana González	ana-gonzalez-8447	Atención rápida y presupuesto sin cargo. Disponible fines de semana.	+5493855158358	Frías	\N	5.00	23	t	2026-03-25 13:57:20	2026-03-25 13:57:20	23	\N	available
52	Roberto Paz	roberto-paz-7454	Atención rápida y presupuesto sin cargo. Disponible fines de semana.	+5493855116130	Icaño	\N	4.62	21	t	2026-03-25 13:57:20	2026-03-25 13:57:20	19	\N	available
\.


--
-- Name: categories_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.categories_id_seq', 14, true);


--
-- Name: email_verifications_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.email_verifications_id_seq', 2, true);


--
-- Name: failed_jobs_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.failed_jobs_id_seq', 1, false);


--
-- Name: migrations_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.migrations_id_seq', 16, true);


--
-- Name: personal_access_tokens_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.personal_access_tokens_id_seq', 1, false);


--
-- Name: ratings_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.ratings_id_seq', 3, true);


--
-- Name: users_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.users_id_seq', 3, true);


--
-- Name: worker_events_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.worker_events_id_seq', 8, true);


--
-- Name: worker_photos_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.worker_photos_id_seq', 1, false);


--
-- Name: workers_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.workers_id_seq', 52, true);


--
-- Name: categories categories_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.categories
    ADD CONSTRAINT categories_pkey PRIMARY KEY (id);


--
-- Name: categories categories_slug_unique; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.categories
    ADD CONSTRAINT categories_slug_unique UNIQUE (slug);


--
-- Name: category_worker category_worker_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.category_worker
    ADD CONSTRAINT category_worker_pkey PRIMARY KEY (worker_id, category_id);


--
-- Name: email_verifications email_verifications_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.email_verifications
    ADD CONSTRAINT email_verifications_pkey PRIMARY KEY (id);


--
-- Name: failed_jobs failed_jobs_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.failed_jobs
    ADD CONSTRAINT failed_jobs_pkey PRIMARY KEY (id);


--
-- Name: failed_jobs failed_jobs_uuid_unique; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.failed_jobs
    ADD CONSTRAINT failed_jobs_uuid_unique UNIQUE (uuid);


--
-- Name: migrations migrations_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.migrations
    ADD CONSTRAINT migrations_pkey PRIMARY KEY (id);


--
-- Name: personal_access_tokens personal_access_tokens_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.personal_access_tokens
    ADD CONSTRAINT personal_access_tokens_pkey PRIMARY KEY (id);


--
-- Name: personal_access_tokens personal_access_tokens_token_unique; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.personal_access_tokens
    ADD CONSTRAINT personal_access_tokens_token_unique UNIQUE (token);


--
-- Name: ratings ratings_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.ratings
    ADD CONSTRAINT ratings_pkey PRIMARY KEY (id);


--
-- Name: users users_email_unique; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.users
    ADD CONSTRAINT users_email_unique UNIQUE (email);


--
-- Name: users users_google_id_unique; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.users
    ADD CONSTRAINT users_google_id_unique UNIQUE (google_id);


--
-- Name: users users_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.users
    ADD CONSTRAINT users_pkey PRIMARY KEY (id);


--
-- Name: worker_events worker_events_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.worker_events
    ADD CONSTRAINT worker_events_pkey PRIMARY KEY (id);


--
-- Name: worker_photos worker_photos_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.worker_photos
    ADD CONSTRAINT worker_photos_pkey PRIMARY KEY (id);


--
-- Name: workers workers_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.workers
    ADD CONSTRAINT workers_pkey PRIMARY KEY (id);


--
-- Name: workers workers_slug_unique; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.workers
    ADD CONSTRAINT workers_slug_unique UNIQUE (slug);


--
-- Name: email_verifications_email_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX email_verifications_email_index ON public.email_verifications USING btree (email);


--
-- Name: password_resets_email_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX password_resets_email_index ON public.password_resets USING btree (email);


--
-- Name: personal_access_tokens_tokenable_type_tokenable_id_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX personal_access_tokens_tokenable_type_tokenable_id_index ON public.personal_access_tokens USING btree (tokenable_type, tokenable_id);


--
-- Name: ratings_ip_address_worker_id_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX ratings_ip_address_worker_id_index ON public.ratings USING btree (ip_address, worker_id);


--
-- Name: ratings_worker_id_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX ratings_worker_id_index ON public.ratings USING btree (worker_id);


--
-- Name: worker_events_created_at_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX worker_events_created_at_index ON public.worker_events USING btree (created_at);


--
-- Name: worker_events_type_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX worker_events_type_index ON public.worker_events USING btree (type);


--
-- Name: workers_is_active_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX workers_is_active_index ON public.workers USING btree (is_active);


--
-- Name: workers_town_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX workers_town_index ON public.workers USING btree (town);


--
-- Name: category_worker category_worker_category_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.category_worker
    ADD CONSTRAINT category_worker_category_id_foreign FOREIGN KEY (category_id) REFERENCES public.categories(id) ON DELETE CASCADE;


--
-- Name: category_worker category_worker_worker_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.category_worker
    ADD CONSTRAINT category_worker_worker_id_foreign FOREIGN KEY (worker_id) REFERENCES public.workers(id) ON DELETE CASCADE;


--
-- Name: ratings ratings_worker_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.ratings
    ADD CONSTRAINT ratings_worker_id_foreign FOREIGN KEY (worker_id) REFERENCES public.workers(id) ON DELETE CASCADE;


--
-- Name: worker_events worker_events_worker_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.worker_events
    ADD CONSTRAINT worker_events_worker_id_foreign FOREIGN KEY (worker_id) REFERENCES public.workers(id) ON DELETE CASCADE;


--
-- Name: worker_photos worker_photos_worker_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.worker_photos
    ADD CONSTRAINT worker_photos_worker_id_foreign FOREIGN KEY (worker_id) REFERENCES public.workers(id) ON DELETE CASCADE;


--
-- PostgreSQL database dump complete
--

\unrestrict lfzZRcpJlZ4cxZHMPU2fnieoZ4PsnFJTaXSFkjbTjgXNAbqEY72heeKqmL3yq0C

