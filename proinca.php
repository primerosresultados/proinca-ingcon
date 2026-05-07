<?php

// PROINCA — página de marca. Usa el handler POST de index.php para leads.
if (getenv('DEV_PREVIEW') === '1') {
    require __DIR__ . '/lib/dev-stubs.php';
} else {
    require __DIR__ . '/lib/bootstrap.php';
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'submit_lead') {
    require __DIR__ . '/index.php';
    return;
}

$brand = [
    'key'             => 'proinca',
    'name'            => 'PROINCA',
    'kind'            => 'Construcción',
    'eyebrow'         => '01 — Construcción',
    'tagline'         => 'Línea de construcción. Ejecutamos proyectos habitacionales y comerciales con supervisión técnica profesional, gestión en terreno y control riguroso de calidad y plazos.',
    'description'     => 'PROINCA — línea de construcción de Alexis Bello. Proyectos habitacionales y comerciales con supervisión técnica profesional en Pucón, Villarrica y La Araucanía.',
    'accent'          => '#C8A24A',
    'accent_dk'       => '#9C7C2D',
    'hero_bg'         => 'https://images.unsplash.com/photo-1504307651254-35680f356dfd?w=1800&q=85&auto=format&fit=crop',
    'chips'           => ['Habitacional', 'Comercial', 'Supervisión técnica', 'Dirección de proyectos', 'Remodelaciones'],

    'services_title'  => 'Construimos con criterio. <em>Entregamos a tiempo.</em>',
    'services_lede'   => 'Cinco frentes de trabajo en terreno, coordinados por un mismo criterio técnico.',
    'services'        => [
        ['title' => 'Construcción habitacional', 'text' => 'Casas, segundas residencias y edificaciones residenciales. Fundaciones, estructura y terminaciones bajo control profesional.'],
        ['title' => 'Construcción comercial',    'text' => 'Locales, oficinas y obras menores comerciales con foco en plazos, costos y normativa local.'],
        ['title' => 'Supervisión técnica',       'text' => 'Inspección técnica de obra (ITO) en proyectos propios y de terceros: control de avance, calidad y registro documental.'],
        ['title' => 'Dirección de proyectos',    'text' => 'Coordinación integral entre arquitectura, especialidades, mandante y municipio. Una sola interlocución.'],
        ['title' => 'Remodelaciones y ampliaciones', 'text' => 'Reformas estructurales y ampliaciones con cálculo asociado. Trabajo coordinado con INGEPUCON.'],
        ['title' => 'Gestión municipal',         'text' => 'Tramitación de permisos de edificación, recepción y regularizaciones ante la DOM correspondiente.'],
    ],

    'projects'        => [
        ['title' => 'Casa lago Villarrica',  'type' => 'Habitacional · 220 m²',  'text' => 'Vivienda en hormigón y madera laminada con vista al volcán Villarrica. Coordinación full con arquitectura.', 'img' => 'https://images.unsplash.com/photo-1600585154340-be6161a56a0c?w=1200&q=85&auto=format&fit=crop'],
        ['title' => 'Local Pucón centro',    'type' => 'Comercial · 140 m²',     'text' => 'Habilitación comercial completa: estructura, instalaciones, terminaciones y permisos finales.', 'img' => 'https://images.unsplash.com/photo-1486325212027-8081e485255e?w=1200&q=85&auto=format&fit=crop'],
        ['title' => 'Ampliación familiar',   'type' => 'Remodelación · 80 m²',   'text' => 'Ampliación de vivienda existente con refuerzo estructural calculado por INGEPUCON y ejecución por PROINCA.', 'img' => 'https://images.unsplash.com/photo-1503387762-592deb58ef4e?w=1200&q=85&auto=format&fit=crop'],
    ],

    'why_title'       => '¿Por qué <em>PROINCA?</em>',
    'why'             => [
        ['title' => 'Criterio técnico', 'text' => 'Cada decisión en obra tiene respaldo de cálculo. Sin atajos.'],
        ['title' => 'Plazos respetados', 'text' => 'Cartas Gantt realistas, hitos medibles y comunicación semanal con mandante.'],
        ['title' => 'Costos transparentes', 'text' => 'Itemizado claro, sin "extras" sorpresivos. Si algo cambia, se conversa.'],
        ['title' => 'Conocimiento local', 'text' => 'Trabajamos donde vivimos. Conocemos suelos, clima y oficinas de la región.'],
    ],

    'form_options'    => [
        'Construcción habitacional',
        'Construcción comercial',
        'Supervisión técnica',
        'Remodelación / ampliación',
        'Dirección de proyecto',
    ],
];

require __DIR__ . '/components/proinca_template.php';
