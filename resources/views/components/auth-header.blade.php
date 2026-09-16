@props([
    'title',
    'description',
])

<div class="flex w-full flex-col text-center">
    <flux:heading size="xl"><?= e($title) ?></flux:heading>
    <flux:subheading><?= e($description) ?></flux:subheading>
</div>
