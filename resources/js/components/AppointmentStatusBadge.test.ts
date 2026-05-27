import { mount } from '@vue/test-utils';
import { afterEach, beforeEach, describe, expect, it, vi } from 'vitest';

import AppointmentStatusBadge from './AppointmentStatusBadge.vue';

describe('AppointmentStatusBadge', () => {
    beforeEach(() => {
        vi.useFakeTimers();
    });

    afterEach(() => {
        vi.useRealTimers();
    });

    it('renders human-readable text for a given status', () => {
        const wrapper = mount(AppointmentStatusBadge, {
            props: {
                status: 'checked-in',
                appointmentTime: '2099-01-01T12:00:00.000Z',
            },
        });

        expect(wrapper.get('[role="status"]').text()).toContain('Checked In');
    });

    it('appends Soon when status is scheduled and appointmentTime is within 15 minutes', () => {
        const now = new Date('2026-05-16T12:00:00.000Z');
        vi.setSystemTime(now);

        const wrapper = mount(AppointmentStatusBadge, {
            props: {
                status: 'scheduled',
                appointmentTime: new Date(
                    now.getTime() + 10 * 60 * 1000,
                ).toISOString(),
            },
        });

        expect(wrapper.get('[role="status"]').text()).toContain('Soon');
    });

    it('does not append Soon when appointmentTime is more than 15 minutes away', () => {
        const now = new Date('2026-05-16T12:00:00.000Z');
        vi.setSystemTime(now);

        const wrapper = mount(AppointmentStatusBadge, {
            props: {
                status: 'scheduled',
                appointmentTime: new Date(
                    now.getTime() + 30 * 60 * 1000,
                ).toISOString(),
            },
        });

        expect(wrapper.get('[role="status"]').text()).not.toContain('Soon');
    });

    it('emits click event with status string when badge is clicked', async () => {
        const status = 'completed';
        const wrapper = mount(AppointmentStatusBadge, {
            props: {
                status,
                appointmentTime: '2099-01-01T12:00:00.000Z',
            },
        });

        await wrapper.get('button').trigger('click');

        expect(wrapper.emitted('click')?.[0]?.[0]).toBe(status);
    });

    it('keeps compact badges accessible with tooltip and labels', () => {
        const wrapper = mount(AppointmentStatusBadge, {
            props: {
                status: 'completed',
                appointmentTime: '2099-01-01T12:00:00.000Z',
                compact: true,
            },
        });

        const button = wrapper.get('button');
        const badge = wrapper.get('[role="status"]');

        expect(button.attributes('aria-label')).toBe('Appointment status: Completed');
        expect(badge.attributes('title')).toBe('Completed');
        expect(badge.attributes('aria-label')).toBe('Completed');
        expect(badge.text()).toBe('');
    });
});
