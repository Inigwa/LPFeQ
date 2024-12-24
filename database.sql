--
-- PostgreSQL database dump
--

-- Dumped from database version 16.2 (Ubuntu 16.2-1.pgdg23.04+1)
-- Dumped by pg_dump version 16.2 (Ubuntu 16.2-1.pgdg23.04+1)

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

SET default_tablespace = '';

SET default_table_access_method = heap;

--
-- Name: equipment; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.equipment (
    eq_id integer NOT NULL,
    registration_number integer NOT NULL,
    operator_name character varying(50) NOT NULL
);


ALTER TABLE public.equipment OWNER TO postgres;

--
-- Name: equipment_eq_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.equipment_eq_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.equipment_eq_id_seq OWNER TO postgres;

--
-- Name: equipment_eq_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.equipment_eq_id_seq OWNED BY public.equipment.eq_id;


--
-- Name: equipment_lpf; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.equipment_lpf (
    eq_id integer NOT NULL,
    lpf_id integer NOT NULL
);


ALTER TABLE public.equipment_lpf OWNER TO postgres;

--
-- Name: lpf; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.lpf (
    lpf_id integer NOT NULL,
    lpf_name character varying(30) NOT NULL,
    lpf_adress character varying(50) NOT NULL,
    lpf_work_schedule character varying(20)
);


ALTER TABLE public.lpf OWNER TO postgres;

--
-- Name: lpf_lpf_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.lpf_lpf_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.lpf_lpf_id_seq OWNER TO postgres;

--
-- Name: lpf_lpf_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.lpf_lpf_id_seq OWNED BY public.lpf.lpf_id;


--
-- Name: equipment eq_id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.equipment ALTER COLUMN eq_id SET DEFAULT nextval('public.equipment_eq_id_seq'::regclass);


--
-- Name: lpf lpf_id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.lpf ALTER COLUMN lpf_id SET DEFAULT nextval('public.lpf_lpf_id_seq'::regclass);


--
-- Data for Name: equipment; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.equipment (eq_id, registration_number, operator_name) FROM stdin;
1	12345	Иванов Иван
2	67890	Петров Петр
\.


--
-- Data for Name: equipment_lpf; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.equipment_lpf (eq_id, lpf_id) FROM stdin;
1	1
2	1
1	2
\.


--
-- Data for Name: lpf; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.lpf (lpf_id, lpf_name, lpf_adress, lpf_work_schedule) FROM stdin;
1	ЛПФ-1	ул. Лесная, 10	8:00-18:00
2	ЛПФ-2	ул. Полевая, 5	9:00-17:00
\.


--
-- Name: equipment_eq_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.equipment_eq_id_seq', 2, true);


--
-- Name: lpf_lpf_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.lpf_lpf_id_seq', 2, true);


--
-- Name: equipment equipment_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.equipment
    ADD CONSTRAINT equipment_pkey PRIMARY KEY (eq_id);


--
-- Name: lpf lpf_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.lpf
    ADD CONSTRAINT lpf_pkey PRIMARY KEY (lpf_id);


--
-- Name: equipment_lpf fk_eq_id; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.equipment_lpf
    ADD CONSTRAINT fk_eq_id FOREIGN KEY (eq_id) REFERENCES public.equipment(eq_id) ON DELETE CASCADE;


--
-- Name: equipment_lpf fk_lpf_id; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.equipment_lpf
    ADD CONSTRAINT fk_lpf_id FOREIGN KEY (lpf_id) REFERENCES public.lpf(lpf_id) ON DELETE CASCADE;


--
-- PostgreSQL database dump complete
--

