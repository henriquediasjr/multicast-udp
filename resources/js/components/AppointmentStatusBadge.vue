<script setup lang="ts">
import { computed } from 'vue';

const appointmentStatuses = [
    'scheduled',
    'checked-in',
    'in-progress',
    'completed',
    'cancelled',
    'no-show',
] as const;

type AppointmentStatus = (typeof appointmentStatuses)[number];

interface Props {
    status: AppointmentStatus;
    appointmentTime: string;
    compact?: boolean;
}

const statusLabels: Record<AppointmentStatus, string> = {
    scheduled: 'Scheduled',
    'checked-in': 'Checked In',
    'in-progress': 'In Progress',
    completed: 'Completed',
    cancelled: 'Cancelled',
    'no-show': 'No Show',
};

const props = withDefaults(defineProps<Props>(), {
    compact: false,
});

const emit = defineEmits<{
    (event: 'click', status: AppointmentStatus): void;
}>();

const displayText = computed(() => {
    const label = statusLabels[props.status];

    if (props.status !== 'scheduled') {
        return label;
    }

    const appointmentDate = new Date(props.appointmentTime);

    if (Number.isNaN(appointmentDate.getTime())) {
        return label;
    }

    const timeUntilAppointment = appointmentDate.getTime() - Date.now();
    const soonThreshold = 15 * 60 * 1000;

    return timeUntilAppointment >= 0 && timeUntilAppointment <= soonThreshold
        ? `${label} - Soon`
        : label;
});

const buttonLabel = computed(() => `Appointment status: ${displayText.value}`);

const badgeClasses = computed(() => [
    'badge',
    `badge--${props.status}`,
    {
        'badge--compact': props.compact,
    },
]);

const handleClick = () => {
    emit('click', props.status);
};
</script>

<template>
    <button
        type="button"
        class="badge-button"
        :aria-label="buttonLabel"
        @click="handleClick"
    >
        <span
            :class="badgeClasses"
            :title="props.compact ? displayText : undefined"
            :aria-label="displayText"
            role="status"
        >
            <span v-if="!props.compact">{{ displayText }}</span>
        </span>
    </button>
</template>

<style scoped>
.badge-button {
    display: inline-flex;
    padding: 0;
    border: 0;
    background: none;
    font: inherit;
    cursor: pointer;
}

.badge-button:focus-visible {
    outline: 2px solid #1d4ed8;
    outline-offset: 2px;
    border-radius: 9999px;
}

.badge {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 0.25rem 0.5rem;
    border-radius: 9999px;
    font-size: 0.875rem;
    line-height: 1;
    color: #ffffff;
}

.badge--compact {
    width: 0.75rem;
    height: 0.75rem;
    padding: 0;
}

.badge--scheduled {
    background-color: #2563eb;
}

.badge--checked-in {
    background-color: #0891b2;
}

.badge--in-progress {
    background-color: #7c3aed;
}

.badge--completed {
    background-color: #16a34a;
}

.badge--cancelled {
    background-color: #dc2626;
}

.badge--no-show {
    background-color: #6b7280;
}
</style>
