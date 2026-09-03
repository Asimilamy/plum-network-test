import { createApp } from 'vue';

const pages = import.meta.glob('./pages/**/*.vue', { eager: true });

const el = document.getElementById('app');
const { name, props, shared } = JSON.parse(el.dataset.page);

const page = pages[`./pages/${name}.vue`];

if (! page) {
    throw new Error(`Vue page [${name}] was not found in resources/js/pages.`);
}

createApp(page.default, { ...props, shared }).mount(el);
