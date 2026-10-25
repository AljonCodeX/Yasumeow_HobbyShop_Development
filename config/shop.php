<?php

return [
    'hours'     => '8:00 AM – 6:00 PM',
    'location'  => 'View on Google Maps',
    'map'       => 'https://maps.app.goo.gl/5JJaqa135g6njbxC8',
    'messenger' => 'https://m.me/inanotherworldwithyasumeow',
    'photo' => 'images/shop-building.webp', // file in public/images
    'embed' => 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d15411.075638920842!2d120.8985975173828!3d15.060892900000002!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x33970387f7ed710d%3A0x37c9dbcf2b36aa7a!2sYasumeow\'s%20Internet%20Cafe%20%26%20Hobby%20Shop%20In%20Another%20World!5e0!3m2!1sen!2sph!4v1792941154392!5m2!1sen!2sph',
    'gallery' => [
        'public/images/about/about-1.webp',
        'public/images/about/about-1.webp',
        'public/images/about/about-1.webp',
        'public/images/about/about-1.webp',
    ],

    'offers' => [
        ['icon' => 'bi-bag-fill',    'title' => 'Shop',    'text' => 'Cards, beys, kits, figures and more.', 'url' => '/shop'],
        ['icon' => 'bi-controller',  'title' => 'Play',    'text' => 'Try board games, toy games and PS5.',  'url' => '/games'],
        ['icon' => 'bi-trophy-fill', 'title' => 'Compete', 'text' => 'Join tournaments and races.',          'url' => '/events'],
    ],
];