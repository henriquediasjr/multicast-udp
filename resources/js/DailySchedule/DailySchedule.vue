<script setup lang="ts">
import { onMounted, ref } from 'vue';

import AppointmentStatusBadge from '../components/AppointmentStatusBadge.vue';

type AppointmentStatus =
    | 'scheduled'
    | 'checked-in'
    | 'in-progress'
    | 'completed'
    | 'cancelled'
    | 'no-show';

interface Appointment {
    id: number;
    patientName: string;
    status: AppointmentStatus;
    appointmentTime: string;
    timeLabel: string;
}

interface DailyScheduleResponse {
    date: string;
    userId: number | null;
    data: Appointment[];
}

interface Props {
    userId?: string | null;
    today: string;
    apiUrl: string;
}

const props = defineProps<Props>();

const appointments = ref<Appointment[]>([]);
const errorMessage = ref('');
const isLoading = ref(true);

const fetchSchedule = async () => {
    isLoading.value = true;
    errorMessage.value = '';

    try {
        const url = new URL(props.apiUrl, window.location.origin);
        url.searchParams.set('date', props.today);

        if (props.userId) {
            url.searchParams.set('user_id', props.userId);
        }

        const response = await fetch(url.toString(), {
            headers: {
                Accept: 'application/json',
            },
        });

        if (!response.ok) {
            throw new Error('Unable to load the daily schedule.');
        }

        const payload: DailyScheduleResponse = await response.json();
        appointments.value = payload.data;
    } catch (error) {
        errorMessage.value =
            error instanceof Error
                ? error.message
                : 'Unable to load the daily schedule.';
    } finally {
        isLoading.value = false;
    }
};

onMounted(fetchSchedule);
</script>

<template>
    <section class="daily-schedule" :aria-busy="isLoading">
        <p class="daily-schedule__date">Showing appointments for {{ today }}</p>

        <p v-if="isLoading" class="daily-schedule__message">Loading schedule...</p>

        <p
            v-else-if="errorMessage"
            class="daily-schedule__message daily-schedule__message--error"
            role="alert"
        >
            {{ errorMessage }}
        </p>

        <p v-else-if="appointments.length === 0" class="daily-schedule__message">
            No appointments for this day.
        </p>

        <ul v-else class="daily-schedule__list">
            <li
                v-for="appointment in appointments"
                :key="appointment.id"
                class="daily-schedule__item"
            >
                <div>
                    <p class="daily-schedule__time">{{ appointment.timeLabel }}</p>
                    <p class="daily-schedule__patient">
                        {{ appointment.patientName }}
                    </p>
                </div>

                <AppointmentStatusBadge
                    :status="appointment.status"
                    :appointment-time="appointment.appointmentTime"
                />
            </li>
        </ul>
    </section>
</template>

<style scoped>
.daily-schedule {
    display: grid;
    gap: 1rem;
}

.daily-schedule__date {
    margin: 0;
    font-size: 0.875rem;
    color: #4b5563;
}

.daily-schedule__message {
    margin: 0;
    color: #111827;
}

.daily-schedule__message--error {
    color: #b91c1c;
}

.daily-schedule__list {
    display: grid;
    gap: 0.75rem;
    margin: 0;
    padding: 0;
    list-style: none;
}

.daily-schedule__item {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 1rem;
    padding: 1rem;
    border: 1px solid #e5e7eb;
    border-radius: 0.75rem;
    background-color: #ffffff;
}

.daily-schedule__time,
.daily-schedule__patient {
    margin: 0;
}

.daily-schedule__time {
    font-size: 0.875rem;
    color: #4b5563;
}

.daily-schedule__patient {
    margin-top: 0.25rem;
    font-weight: 600;
    color: #111827;
}
</style>
