@props([
    'status',
])

<?php if ($status): ?>
    <div <?= e($attributes->merge(['class' => 'font-medium text-sm text-green-600'])) ?>>
        <?= e($status) ?>
    </div>
<?php endif; ?>
