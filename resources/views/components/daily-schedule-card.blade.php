<section class="mt-8 p-6 bg-white dark:bg-gray-800/50 dark:bg-gradient-to-bl from-gray-700/50 via-transparent dark:ring-1 dark:ring-inset dark:ring-white/5 rounded-lg shadow-2xl shadow-gray-500/20 dark:shadow-none">
    <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Daily Schedule</h2>
    <p class="mt-4 text-sm leading-relaxed text-gray-500 dark:text-gray-400">
        Vue loads the appointments for the selected day from a Laravel API endpoint.
    </p>

    <div
        class="mt-6"
        data-component="daily-schedule"
        data-user-id="{{ auth()->id() ?? '' }}"
        data-today="{{ now()->format('Y-m-d') }}"
        data-api-url="{{ route('api.schedule.daily') }}"
    ></div>
</section>
