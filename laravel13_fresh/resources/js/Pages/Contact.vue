<script setup lang="ts">
import Container from '@/Components/Container.vue';
import Icon from '@/Components/Icons/Icon.vue';
import InputError from '@/Components/InputError.vue';
import Heading2 from '@/Components/Web/Heading2.vue';
import Heading3 from '@/Components/Web/Heading3.vue';
import WebHeader from '@/Layouts/WebHeader.vue';
import WebLayout from '@/Layouts/WebLayout.vue';
import { useForm } from '@inertiajs/vue3';
import Swal from 'sweetalert2';
import { iContact } from '../interfaces';

const props = defineProps({
    contacts: Array<iContact>,
    notification: Object,
});

const form = useForm({
    name: '',
    email: '',
    phone: '',
    subject: '',
    content: '',
});

const submit = () => {
    form.post(route('feedback'), {
        onSuccess: () => {
            // form.reset()
            Swal.fire({
                icon: 'success',
                text: props?.notification?.success,
            });
        },
    });
};
</script>

<template>
    <WebLayout title="Contact Us">
        <template #header>
            <WebHeader>
                <Heading2>Contact Us</Heading2>
            </WebHeader>
        </template>
        <Container>
            <div
                class="my-16 grid grid-cols-1 items-center gap-16 md:grid-cols-3"
            >
                <div class="flex flex-col gap-4 py-8">
                    <div
                        v-for="{ icon, title, content } in contacts"
                        class="flex items-center gap-3 rounded-xl bg-white px-3 py-5 shadow"
                    >
                        <div
                            class="h-14 w-14 flex-none overflow-hidden rounded-full p-1"
                        >
                            <Icon
                                class="h-full w-full object-contain text-secondary-default"
                                :type="icon ?? undefined"
                            />
                        </div>
                        <div class="flex flex-col">
                            <div
                                class="text-lg font-semibold text-primary-default"
                                v-text="title"
                            ></div>
                            <div
                                class="text-base font-medium text-secondary-600"
                                v-text="content"
                            ></div>
                        </div>
                    </div>
                </div>
                <div
                    class="rounded-xl bg-white px-8 py-16 shadow-xl md:col-span-2"
                >
                    <Heading3 class="mb-8 capitalize"
                        >Send us a message</Heading3
                    >
                    <form @submit.prevent="submit">
                        <div class="grid grid-cols-1 md:grid-cols-2 md:gap-3">
                            <div class="group relative z-0 mb-5 w-full">
                                <input
                                    v-model="form.name"
                                    id="floating_name"
                                    class="peer block w-full appearance-none border-0 border-b-2 border-gray-300 bg-transparent px-0 py-2.5 text-sm text-gray-900 focus:border-primary-600 focus:outline-none focus:ring-0 dark:border-gray-600 dark:text-white dark:focus:border-primary-500"
                                    placeholder=" "
                                    required
                                />
                                <label
                                    for="floating_name"
                                    class="absolute top-3 -z-10 origin-[0] -translate-y-6 scale-75 transform text-sm text-gray-500 duration-300 peer-placeholder-shown:translate-y-0 peer-placeholder-shown:scale-100 peer-focus:start-0 peer-focus:-translate-y-6 peer-focus:scale-75 peer-focus:font-medium peer-focus:text-primary-600 rtl:peer-focus:left-auto rtl:peer-focus:translate-x-1/4 dark:text-gray-400 peer-focus:dark:text-primary-500"
                                    >Name</label
                                >
                                <InputError :message="form.errors.name" />
                            </div>
                            <div class="group relative z-0 mb-5 w-full">
                                <input
                                    type="email"
                                    v-model="form.email"
                                    id="floating_email"
                                    class="peer block w-full appearance-none border-0 border-b-2 border-gray-300 bg-transparent px-0 py-2.5 text-sm text-gray-900 focus:border-primary-600 focus:outline-none focus:ring-0 dark:border-gray-600 dark:text-white dark:focus:border-primary-500"
                                    placeholder=" "
                                    required
                                />
                                <label
                                    for="floating_email"
                                    class="absolute top-3 -z-10 origin-[0] -translate-y-6 scale-75 transform text-sm text-gray-500 duration-300 peer-placeholder-shown:translate-y-0 peer-placeholder-shown:scale-100 peer-focus:start-0 peer-focus:-translate-y-6 peer-focus:scale-75 peer-focus:font-medium peer-focus:text-primary-600 rtl:peer-focus:left-auto rtl:peer-focus:translate-x-1/4 dark:text-gray-400 peer-focus:dark:text-primary-500"
                                    >Email address</label
                                >
                                <InputError :message="form.errors.email" />
                            </div>
                            <div class="group relative z-0 mb-5 w-full">
                                <input
                                    v-model="form.phone"
                                    id="floating_phone"
                                    class="peer block w-full appearance-none border-0 border-b-2 border-gray-300 bg-transparent px-0 py-2.5 text-sm text-gray-900 focus:border-primary-600 focus:outline-none focus:ring-0 dark:border-gray-600 dark:text-white dark:focus:border-primary-500"
                                    placeholder=" "
                                    required
                                />
                                <label
                                    for="floating_phone"
                                    class="absolute top-3 -z-10 origin-[0] -translate-y-6 scale-75 transform text-sm text-gray-500 duration-300 peer-placeholder-shown:translate-y-0 peer-placeholder-shown:scale-100 peer-focus:start-0 peer-focus:-translate-y-6 peer-focus:scale-75 peer-focus:font-medium peer-focus:text-primary-600 rtl:peer-focus:left-auto rtl:peer-focus:translate-x-1/4 dark:text-gray-400 peer-focus:dark:text-primary-500"
                                    >Phone Number</label
                                >
                                <InputError :message="form.errors.phone" />
                            </div>
                            <div class="group relative z-0 mb-5 w-full">
                                <input
                                    v-model="form.subject"
                                    id="floating_subject"
                                    class="peer block w-full appearance-none border-0 border-b-2 border-gray-300 bg-transparent px-0 py-2.5 text-sm text-gray-900 focus:border-primary-600 focus:outline-none focus:ring-0 dark:border-gray-600 dark:text-white dark:focus:border-primary-500"
                                    placeholder=" "
                                    required
                                />
                                <label
                                    for="floating_subject"
                                    class="absolute top-3 -z-10 origin-[0] -translate-y-6 scale-75 transform text-sm text-gray-500 duration-300 peer-placeholder-shown:translate-y-0 peer-placeholder-shown:scale-100 peer-focus:start-0 peer-focus:-translate-y-6 peer-focus:scale-75 peer-focus:font-medium peer-focus:text-primary-600 rtl:peer-focus:left-auto rtl:peer-focus:translate-x-1/4 dark:text-gray-400 peer-focus:dark:text-primary-500"
                                    >Subject</label
                                >
                                <InputError :message="form.errors.subject" />
                            </div>
                        </div>
                        <div class="group relative z-0 mb-5 w-full">
                            <textarea
                                v-model="form.content"
                                id="floating_content"
                                class="peer block w-full appearance-none border-0 border-b-2 border-gray-300 bg-transparent px-0 py-2.5 text-sm text-gray-900 focus:border-primary-600 focus:outline-none focus:ring-0 dark:border-gray-600 dark:text-white dark:focus:border-primary-500"
                                placeholder=" "
                                required
                                rows="5"
                            ></textarea>
                            <label
                                for="floating_content"
                                class="absolute top-3 -z-10 origin-[0] -translate-y-6 scale-75 transform text-sm text-gray-500 duration-300 peer-placeholder-shown:translate-y-0 peer-placeholder-shown:scale-100 peer-focus:start-0 peer-focus:-translate-y-6 peer-focus:scale-75 peer-focus:font-medium peer-focus:text-primary-600 rtl:peer-focus:left-auto rtl:peer-focus:translate-x-1/4 dark:text-gray-400 peer-focus:dark:text-primary-500"
                                >Message</label
                            >
                            <InputError :message="form.errors.content" />
                        </div>
                        <button
                            :disabled="form.processing"
                            type="submit"
                            class="me-2 inline-flex items-center rounded-full px-5 py-2.5"
                            :class="{
                                'border border-gray-200 bg-white text-sm font-medium text-gray-900 hover:bg-gray-100 hover:text-primary-700 focus:z-10 focus:text-primary-700 focus:outline-none focus:ring-4 focus:ring-primary-700 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white':
                                    form.processing,
                                'mb-2 bg-gradient-to-r from-primary-500 via-primary-600 to-primary-700 text-center text-sm font-medium text-white hover:bg-gradient-to-br focus:outline-none focus:ring-4 focus:ring-primary-300 dark:focus:ring-primary-800':
                                    !form.processing,
                            }"
                        >
                            <svg
                                v-if="form.processing"
                                aria-hidden="true"
                                role="status"
                                class="me-3 inline h-4 w-4 animate-spin text-gray-200 dark:text-gray-600"
                                viewBox="0 0 100 101"
                                fill="none"
                                xmlns="http://www.w3.org/2000/svg"
                            >
                                <path
                                    d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z"
                                    fill="currentColor"
                                />
                                <path
                                    d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z"
                                    fill="#1C64F2"
                                />
                            </svg>
                            <!-- <svg v-else class="w-3.5 h-3.5 me-2" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                fill="currentColor" viewBox="0 0 18 21">
                                <path
                                    d="M15 12a1 1 0 0 0 .962-.726l2-7A1 1 0 0 0 17 3H3.77L3.175.745A1 1 0 0 0 2.208 0H1a1 1 0 0 0 0 2h.438l.6 2.255v.019l2 7 .746 2.986A3 3 0 1 0 9 17a2.966 2.966 0 0 0-.184-1h2.368c-.118.32-.18.659-.184 1a3 3 0 1 0 3-3H6.78l-.5-2H15Z" />
                            </svg> -->
                            <svg
                                v-else
                                class="me-2 h-3.5 w-3.5"
                                width="512"
                                height="512"
                                viewBox="0 0 512 512"
                                fill="none"
                                xmlns="http://www.w3.org/2000/svg"
                            >
                                <path
                                    d="M53.1197 199.939L453.12 48.5494C454.563 47.9975 456.135 47.8752 457.646 48.1973C459.157 48.5194 460.542 49.2722 461.634 50.3646C462.727 51.4571 463.48 52.8425 463.802 54.3534C464.124 55.8644 464.002 57.4364 463.45 58.8794L312.06 458.879C311.458 460.398 310.405 461.697 309.043 462.599C307.681 463.502 306.075 463.966 304.441 463.929C302.808 463.892 301.225 463.356 299.905 462.392C298.585 461.429 297.592 460.084 297.06 458.539L229.66 292.449C228.874 290.096 227.551 287.957 225.797 286.202C224.042 284.448 221.903 283.125 219.55 282.339L53.4597 214.999C51.8951 214.479 50.5295 213.487 49.5499 212.161C48.5704 210.834 48.0246 209.238 47.9874 207.589C47.9502 205.941 48.4233 204.321 49.342 202.951C50.2607 201.582 51.5801 200.53 53.1197 199.939V199.939Z"
                                    stroke="currentColor"
                                    stroke-width="32"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                />
                                <path
                                    d="M460 52L227 285"
                                    stroke="currentColor"
                                    stroke-width="32"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                />
                            </svg>

                            {{ form.processing ? 'Sending...' : 'Submit' }}
                        </button>
                    </form>
                </div>
            </div>
            <div class="my-16 overflow-hidden rounded-xl shadow-xl">
                <iframe
                    src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d1015.1233887365738!2d36.83722791169472!3d-1.2932942216159415!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x182f11189bd51acf%3A0xdaaf0fae2426feb7!2sYogi%20Corp%20Ea%20Ltd!5e1!3m2!1sen!2ske!4v1761746570979!5m2!1sen!2ske"
                    width="100%"
                    height="450"
                    style="border: 0"
                    allowfullscreen
                    loading="lazy"
                    referrerpolicy="no-referrer-when-downgrade"
                ></iframe>
            </div>
        </Container>
    </WebLayout>
</template>
