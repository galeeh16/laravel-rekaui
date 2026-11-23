<!--
    Page-level tabs made of links (each tab is its own URL). For in-page panels use `Tabs` (reka-ui).
    Usage:
    <NavTabs>
        <NavTabsLink :href="route('dashboard')" active :count="8">Surat keluar</NavTabsLink>
        <NavTabsLink>Surat masuk</NavTabsLink>
    </NavTabs>
-->
<template>
    <nav class="flex gap-1 overflow-x-auto border-b border-line">
        <slot />
    </nav>
</template>
