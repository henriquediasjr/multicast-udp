import { createApp } from 'vue';

import DailySchedule from './DailySchedule.vue';

export function mountDailySchedule() {
    document
        .querySelectorAll('[data-component="daily-schedule"]')
        .forEach((element) => {
            const { userId = '', today = '', apiUrl = '' } = element.dataset;

            if (!today || !apiUrl) {
                return;
            }

            createApp(DailySchedule, {
                userId: userId || null,
                today,
                apiUrl,
            }).mount(element);
        });
}
