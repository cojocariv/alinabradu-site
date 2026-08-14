<?php
declare(strict_types=1);

require_once __DIR__ . '/helpers.php';
require_once __DIR__ . '/../config/contact.php';

/**
 * @var string $legalPageTitle
 * @var string $legalPageDescription
 */
$legalPageTitle = $legalPageTitle ?? 'Informații legale';
$legalPageDescription = $legalPageDescription ?? '';
$seo = [
    'title' => $legalPageTitle . ' - Alina Bradu',
    'description' => $legalPageDescription,
];
require __DIR__ . '/header.php';
?>
<section class="legal-page max-w-3xl mx-auto px-4 md:px-6 py-10 md:py-14">
  <p class="text-[0.65rem] uppercase tracking-boutique text-gold font-medium mb-2">Informații legale</p>
  <h1 class="font-serif text-3xl md:text-4xl text-ink mb-6 font-medium"><?= e($legalPageTitle) ?></h1>
  <div class="legal-page__body text-ink-soft space-y-4 text-[0.95rem] leading-relaxed">
