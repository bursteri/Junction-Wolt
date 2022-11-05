<template>
    <div class="bg-white">
        <TransitionRoot as="template" :show="open">
            <Dialog as="div" class="relative z-10" @close=";(open = false), (confirmation = false)">
                <TransitionChild as="template" enter="ease-out duration-300" enter-from="opacity-0" enter-to="opacity-100" leave="ease-in duration-200" leave-from="opacity-100" leave-to="opacity-0">
                    <div class="fixed inset-0 hidden bg-gray-500 bg-opacity-75 transition-opacity md:block" />
                </TransitionChild>

                <div class="fixed inset-0 z-10 overflow-y-auto">
                    <div class="flex min-h-full items-stretch justify-center text-center md:items-center md:px-2 lg:px-4">
                        <TransitionChild
                            as="template"
                            enter="ease-out duration-300"
                            enter-from="opacity-0 translate-y-4 md:translate-y-0 md:scale-95"
                            enter-to="opacity-100 translate-y-0 md:scale-100"
                            leave="ease-in duration-200"
                            leave-from="opacity-100 translate-y-0 md:scale-100"
                            leave-to="opacity-0 translate-y-4 md:translate-y-0 md:scale-95">
                            <DialogPanel class="flex w-full transform text-left text-base transition md:my-8 md:max-w-2xl md:px-4 lg:max-w-2xl">
                                <div class="relative flex w-full items-center overflow-hidden bg-white px-4 pt-14 pb-8 shadow-2xl sm:px-6 rounded-xl sm:pt-8 md:p-6 lg:p-8">
                                    <button type="button" class="absolute top-4 right-4 text-gray-400 hover:text-gray-500 sm:top-8 sm:right-6 md:top-6 md:right-6 lg:top-8 lg:right-8" @click=";(open = false), (confirmation = false)">
                                        <span class="sr-only">Close</span>
                                        <XMarkIcon class="h-6 w-6" aria-hidden="true" />
                                    </button>

                                    <div v-if="!confirmation && !step" class="px-4 w-full py-5 sm:p-6">
                                        <h3 class="text-md font-bold leading-6 text-gray-900"><span class="font-extrabold">Use my current location</span></h3>
                                        <form class="mt-2 sm:flex sm:items-center">
                                            <div class="w-full sm:max-w-xs">
                                                <label for="email" class="sr-only">Email</label>
                                                <input type="email" name="email" id="email" v-model="form.dropoff" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-sky-500 focus:ring-sky-500 sm:text-sm" placeholder="you@example.com" />
                                            </div>
                                            <button
                                                @click.prevent=";(loading = true), onClick()"
                                                class="mt-3 inline-flex w-full items-center justify-center rounded-md border border-transparent bg-sky-600 px-4 py-2 font-medium text-white shadow-sm hover:bg-sky-700 focus:outline-none focus:ring-2 focus:ring-sky-500 focus:ring-offset-2 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                                                Confirm
                                            </button>
                                        </form>
                                    </div>
                                    <div v-if="confirmation && !step" class="w-full m-4 pt-12 sm:pt-16">
                                        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                                            <div class="mx-auto max-w-4xl text-center">
                                                <h2 class="text-2xl font-bold tracking-tight text-gray-900 sm:text-4xl">Estimated Delivery</h2>
                                                <p class="mt-3 sm:text-3xl text-gray-600 sm:mt-4">
                                                    <!-- current time + data.time_estimate_minutes, in finnish time -->
                                                    <!-- {{ new Date(Date.now() + (data.time_estimate_minutes * 60000)).toLocaleTimeString('fi-FI', { hour: 'numeric', minute: 'numeric' }) }} -->
                                                    {{ data.dropoff.location.formatted_address }}
                                                </p>
                                            </div>
                                        </div>
                                        <div class="mt-10 bg-white pb-12 sm:pb-16">
                                            <div class="relative">
                                                <div class="absolute inset-0 h-1/2" />
                                                <div class="relative mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                                                    <div class="mx-auto max-w-4xl">
                                                        <dl class="rounded-lg bg-white shadow-lg border border-gray-100 sm:grid sm:grid-cols-2">
                                                            <div class="flex flex-col border-b border-gray-100 p-6 text-center sm:border-0 sm:border-r">
                                                                <dt class="order-2 mt-2 text-lg font-medium leading-6 text-gray-500">Euros</dt>
                                                                <dd class="order-1  text-2xl sm:text-5xl font-bold text-sky-600">
                                                                    {{
                                                                        // turn cents to euros with 2 decimals, with :.2f
                                                                        (data.fee.amount / 100).toLocaleString('fi-FI', { style: 'currency', currency: 'EUR' })
                                                                    }}
                                                                </dd>
                                                            </div>
                                                            <div class="flex flex-col border-t border-b border-gray-100 p-6 text-center sm:border-0 sm:border-l sm:border-r">
                                                                <dt class="order-2 mt-2 text-lg font-medium leading-6 text-gray-500">Minutes</dt>
                                                                <dd class="order-1 text-2xl sm:text-5xl font-bold tracking-tight text-sky-600">{{ data.time_estimate_minutes }}</dd>
                                                            </div>
                                                        </dl>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <button type="submit" @click="step = true" class="flex w-full justify-center rounded-md border border-transparent bg-sky-600 py-3 px-4 text-lg font-semibold text-white shadow-sm hover:bg-sky-700 focus:outline-none focus:ring-2 focus:ring-sky-500 focus:ring-offset-2">
                                            Go To Checkout!
                                        </button>
                                    </div>
                                    <div v-if="!details && step" class="w-full">
                                        <h3 class="text-lg font-bold leading-6 text-gray-900">Payment method</h3>
                                        <div class="w-full -space-y-px mt-5 rounded-md shadow-sm">
                                            <div class="relative rounded-md rounded-b-none border border-gray-300 px-3 py-2 focus-within:z-10 focus-within:border-indigo-600 focus-within:ring-1 focus-within:ring-indigo-600">
                                                <label for="name" class="block text-xs font-medium text-gray-900">Email</label>
                                                <input value="john@dooboo.fi" type="text" name="name" id="name" class="block w-full border-0 p-0 text-gray-900 placeholder-gray-500 focus:ring-0 sm:text-sm" />
                                            </div>
                                            <div class="relative rounded-md rounded-t-none border border-gray-300 px-3 py-2 focus-within:z-10 focus-within:border-indigo-600 focus-within:ring-1 focus-within:ring-indigo-600">
                                                <label for="job-title" class="block text-xs font-medium text-gray-900">Your PIN</label>
                                                <input value="****" type="text" name="job-title" id="job-title" class="block w-full border-0 p-0 text-gray-900 placeholder-gray-500 focus:ring-0 sm:text-sm" />
                                            </div>
                                        </div>
                                        <div class="mt-5">
                                            <button
                                                type="submit"
                                                @click="details = true"
                                                class="flex w-full justify-center rounded-md border border-transparent bg-indigo-600 py-2 px-4 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                                                Sign in
                                            </button>
                                        </div>
                                    </div>
                                    <div v-if="details && step && !externalURL">
                                        <div class="px-4 py-5 sm:p-6">
                                            <h3 class="text-lg font-medium leading-6 text-gray-900">Payment method</h3>
                                            <div class="mt-5">
                                                <div class="rounded-md bg-gray-50 px-6 py-5 sm:flex sm:items-start sm:justify-between">
                                                    <h4 class="sr-only">Visa</h4>
                                                    <div class="sm:flex sm:items-start">
                                                        <svg class="h-8 w-auto sm:h-6 sm:flex-shrink-0" viewBox="0 0 36 24" aria-hidden="true">
                                                            <rect width="36" height="24" fill="#224DBA" rx="4" />
                                                            <path
                                                                fill="#fff"
                                                                d="M10.925 15.673H8.874l-1.538-6c-.073-.276-.228-.52-.456-.635A6.575 6.575 0 005 8.403v-.231h3.304c.456 0 .798.347.855.75l.798 4.328 2.05-5.078h1.994l-3.076 7.5zm4.216 0h-1.937L14.8 8.172h1.937l-1.595 7.5zm4.101-5.422c.057-.404.399-.635.798-.635a3.54 3.54 0 011.88.346l.342-1.615A4.808 4.808 0 0020.496 8c-1.88 0-3.248 1.039-3.248 2.481 0 1.097.969 1.673 1.653 2.02.74.346 1.025.577.968.923 0 .519-.57.75-1.139.75a4.795 4.795 0 01-1.994-.462l-.342 1.616a5.48 5.48 0 002.108.404c2.108.057 3.418-.981 3.418-2.539 0-1.962-2.678-2.077-2.678-2.942zm9.457 5.422L27.16 8.172h-1.652a.858.858 0 00-.798.577l-2.848 6.924h1.994l.398-1.096h2.45l.228 1.096h1.766zm-2.905-5.482l.57 2.827h-1.596l1.026-2.827z" />
                                                        </svg>
                                                        <div class="mt-3 sm:mt-0 sm:ml-4">
                                                            <div class="text-sm font-medium text-gray-900">Ending with 4242</div>
                                                            <div class="mt-1 text-sm text-gray-600 sm:flex sm:items-center">
                                                                <div>Expires 12/24</div>
                                                                <span class="hidden sm:mx-2 sm:inline" aria-hidden="true">&middot;</span>
                                                                <div class="mt-1 sm:mt-0">Last updated on 22 Aug 2022</div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="mt-5">
                                                    <button
                                                        type="submit"
                                                        @click="onSubmit"
                                                        class="flex w-full justify-center rounded-md border border-transparent bg-indigo-600 py-2 px-4 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                                                        Buy Now!
                                                    </button>
                                                </div>

                                                <!-- <div v-if="externalURL" :href="externalURL" target="_blank" ref="externalLink" class="max-w-3xl mt-5 mx-auto flex justify-items-center">
                                                    <a href="#" class="font-medium text-indigo-600 text-center hover:text-indigo-500">start your 14-day free trial</a>
                                                </div> -->
                                            </div>
                                        </div>
                                    </div>
                                    <div v-if="externalURL" class="text-center w-full">
                                        <div >
                                            <a :href="externalURL" target="_blank" ref="externalLink" class="font-extrabold underline text-indigo-600 text-center text-xl hover:text-indigo-500">Track your order &#8594</a>
                                        </div>
                                    </div>
                                </div>
                            </DialogPanel>
                        </TransitionChild>
                    </div>
                </div>
            </Dialog>
        </TransitionRoot>

        <div class="mx-auto max-w-2xl py-16 px-4 sm:py-24 sm:px-6 lg:max-w-7xl lg:px-8">
            <div class="lg:grid lg:grid-cols-2 lg:items-start lg:gap-x-8">
                <!-- Image gallery -->
                <TabGroup as="div" class="flex flex-col-reverse">
                    <!-- Image selector -->
                    <div class="mx-auto mt-6 hidden w-full max-w-2xl sm:block lg:max-w-none">
                        <TabList class="grid grid-cols-4 gap-6">
                            <Tab
                                v-for="image in product.images"
                                :key="image.id"
                                class="relative flex h-24 cursor-pointer items-center justify-center rounded-md bg-white text-sm font-medium uppercase text-gray-900 hover:bg-gray-50 focus:outline-none focus:ring focus:ring-opacity-50 focus:ring-offset-4"
                                v-slot="{ selected }">
                                <span class="sr-only"> {{ image.name }} </span>
                                <span class="absolute inset-0 overflow-hidden rounded-md">
                                    <img :src="image.src" alt="" class="h-full w-full object-cover object-center" />
                                </span>
                                <span :class="[selected ? 'ring-sky-500' : 'ring-transparent', 'pointer-events-none absolute inset-0 rounded-md ring-2 ring-offset-2']" aria-hidden="true" />
                            </Tab>
                        </TabList>
                    </div>

                    <TabPanels class="aspect-w-1 aspect-h-1 w-full">
                        <TabPanel v-for="image in product.images" :key="image.id">
                            <img :src="image.src" :alt="image.alt" class="h-full w-full object-cover object-center sm:rounded-lg" />
                        </TabPanel>
                    </TabPanels>
                </TabGroup>

                <!-- Product info -->
                <div class="mt-10 px-4 sm:mt-16 sm:px-0 lg:mt-0">
                    <h1 class="text-3xl font-bold tracking-tight text-gray-900">
                        {{ product.name }}
                    </h1>

                    <div class="mt-3">
                        <h2 class="sr-only">Product information</h2>
                        <p class="text-4xl tracking-tight text-gray-900 font-mono">
                            {{ product.price }}
                        </p>
                    </div>

                    <div class="mt-6">
                        <h3 class="sr-only">Description</h3>

                        <div class="space-y-6 text-base text-gray-700" v-html="product.description" />
                    </div>

                    <div class="mt-6 relative">
                        <div class="sm:flex-col1 mt-10 flex">
                            <button
                                @click.prevent="open = true"
                                class="max-w-xs flex-1 inline-flex font-bold items-center justify-center rounded-md border border-transparent bg-sky-600 py-3 px-8 text-base text-white hover:bg-sky-700 focus:outline-none focus:ring-2 focus:ring-sky-500 focus:ring-offset-2 focus:ring-offset-gray-50 sm:w-full">
                                <BoltIcon class="-ml-1 mr-2 h-6 w-6" aria-hidden="true" />
                                WoltNow
                            </button>

                            <button type="button" class="ml-4 flex items-center justify-center rounded-md py-3 px-3 text-gray-400 hover:bg-gray-100 hover:text-gray-500">
                                <PhoneIcon class="h-6 w-6 flex-shrink-0" aria-hidden="true" />
                                <span class="sr-only">Add to favorites</span>
                            </button>
                        </div>
                    </div>

                    <section aria-labelledby="details-heading" class="mt-12 rounded-lg shadow-sm border border-gray-200 bg-gray-50 p-4">
                        <div class="flex">
                            <div class="mr-4 flex-shrink-0 self-center">
                                <span class="inline-flex h-14 w-14 items-center justify-center rounded-full bg-gray-500">
                                    <span class="text-xl font-medium leading-none text-white">TW</span>
                                </span>
                            </div>
                            <div>
                                <h4 class="text-lg font-bold">Teemu Wallen</h4>
                                <p class="mt-1">Helsinki, Roihuvuori</p>
                                <div class="mt-2 flex items-center">
                                    <!--
                Heroicon name: mini/star

                Active: "text-gray-900", Default: "text-gray-200"
              -->
                                    <svg class="text-gray-900 h-5 w-5 flex-shrink-0" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                        <path
                                            fill-rule="evenodd"
                                            d="M10.868 2.884c-.321-.772-1.415-.772-1.736 0l-1.83 4.401-4.753.381c-.833.067-1.171 1.107-.536 1.651l3.62 3.102-1.106 4.637c-.194.813.691 1.456 1.405 1.02L10 15.591l4.069 2.485c.713.436 1.598-.207 1.404-1.02l-1.106-4.637 3.62-3.102c.635-.544.297-1.584-.536-1.65l-4.752-.382-1.831-4.401z"
                                            clip-rule="evenodd" />
                                    </svg>

                                    <!-- Heroicon name: mini/star -->
                                    <svg class="text-gray-900 h-5 w-5 flex-shrink-0" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                        <path
                                            fill-rule="evenodd"
                                            d="M10.868 2.884c-.321-.772-1.415-.772-1.736 0l-1.83 4.401-4.753.381c-.833.067-1.171 1.107-.536 1.651l3.62 3.102-1.106 4.637c-.194.813.691 1.456 1.405 1.02L10 15.591l4.069 2.485c.713.436 1.598-.207 1.404-1.02l-1.106-4.637 3.62-3.102c.635-.544.297-1.584-.536-1.65l-4.752-.382-1.831-4.401z"
                                            clip-rule="evenodd" />
                                    </svg>

                                    <!-- Heroicon name: mini/star -->
                                    <svg class="text-gray-900 h-5 w-5 flex-shrink-0" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                        <path
                                            fill-rule="evenodd"
                                            d="M10.868 2.884c-.321-.772-1.415-.772-1.736 0l-1.83 4.401-4.753.381c-.833.067-1.171 1.107-.536 1.651l3.62 3.102-1.106 4.637c-.194.813.691 1.456 1.405 1.02L10 15.591l4.069 2.485c.713.436 1.598-.207 1.404-1.02l-1.106-4.637 3.62-3.102c.635-.544.297-1.584-.536-1.65l-4.752-.382-1.831-4.401z"
                                            clip-rule="evenodd" />
                                    </svg>

                                    <!-- Heroicon name: mini/star -->
                                    <svg class="text-gray-900 h-5 w-5 flex-shrink-0" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                        <path
                                            fill-rule="evenodd"
                                            d="M10.868 2.884c-.321-.772-1.415-.772-1.736 0l-1.83 4.401-4.753.381c-.833.067-1.171 1.107-.536 1.651l3.62 3.102-1.106 4.637c-.194.813.691 1.456 1.405 1.02L10 15.591l4.069 2.485c.713.436 1.598-.207 1.404-1.02l-1.106-4.637 3.62-3.102c.635-.544.297-1.584-.536-1.65l-4.752-.382-1.831-4.401z"
                                            clip-rule="evenodd" />
                                    </svg>

                                    <!-- Heroicon name: mini/star -->
                                    <svg class="text-gray-900 h-5 w-5 flex-shrink-0" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                        <path
                                            fill-rule="evenodd"
                                            d="M10.868 2.884c-.321-.772-1.415-.772-1.736 0l-1.83 4.401-4.753.381c-.833.067-1.171 1.107-.536 1.651l3.62 3.102-1.106 4.637c-.194.813.691 1.456 1.405 1.02L10 15.591l4.069 2.485c.713.436 1.598-.207 1.404-1.02l-1.106-4.637 3.62-3.102c.635-.544.297-1.584-.536-1.65l-4.752-.382-1.831-4.401z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </section>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue'
import { Tab, TabGroup, TabList, TabPanel, TabPanels, Dialog, DialogPanel, TransitionChild, TransitionRoot } from '@headlessui/vue'
import { PhoneIcon, XMarkIcon, BoltIcon } from '@heroicons/vue/24/outline'
import { useForm } from '@inertiajs/inertia-vue3'

import { PlusIcon } from '@heroicons/vue/20/solid'

const form = useForm({
    pickup: 'Arkadiankatu 3-6, 00100 Helsinki',
    dropoff: 'Otakaari 24, 02150 Espoo'
})

const props = defineProps({
    data: Object
})

const data = ref(props.data)
const loading = ref(false)

const step = ref(false)

const product = {
    name: '30W USB-C Power Adapter',
    price: '14.50€',
    rating: 4,
    images: [
        {
            id: 1,
            name: 'Angled view',
            src: '/img/1.jpeg',
            alt: 'Angled front view with bag zipped and handles upright.'
        },
        {
            id: 2,
            name: 'Angled view',
            src: '/img/2.jpeg',
            alt: 'Angled front view with bag zipped and handles upright.'
        },
        {
            id: 3,
            name: 'Angled view',
            src: '/img/3.jpeg',
            alt: 'Angled front view with bag zipped and handles upright.'
        },
        {
            id: 4,
            name: 'Angled view',
            src: '/img/4.jpeg',
            alt: 'Angled front view with bag zipped and handles upright.'
        }
        // More images...
    ],
    description: `
      <p>

The 30W USB‑C Power Adapter offers fast, efficient charging at home, in the office, or on the go. While the power adapter is compatible with any USB‑C device, Apple recommends using it with MacBook Air. You can also use it with select iPhone and iPad Pro models to take advantage of the fast-charging feature.
</p>
    `
}

const onClick = () => {
    axios.post('/delivery-fee', { search: form }).then((response) => {
        data.value = response.data
        loading.value = false
        confirmation.value = true
    })
}

const onSubmit = () => {
    axios.post('/delivery-order', { search: form }).then((response) => {
        externalURL.value = response.data.tracking.url
    })
    // setTimeout(() => {
    //     externalLink.value.click()
    // }, 1000)
}

const handleClick = (slug, job) => {
    loading.value = true
    axios
        .get(route('preview.cache'))
        .then((response) => {
            externalURL.value = job ? URL + '/' + slug + '/' + job + '?preview=' + response.data : URL + '/' + slug + '?preview=' + response.data
        })
        .catch((e) => {})
}

const details = ref(false)

const externalLink = ref()
const externalURL = ref(null)

const open = ref(false)
const confirmation = ref(false)
</script>
