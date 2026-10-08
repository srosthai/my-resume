<script setup lang="ts">
import Icon from '@/components/Icon.vue';
import { cn } from '@/lib/utils';
import { CalendarDate, getLocalTimeZone, today } from '@internationalized/date';
import {
    CalendarCell,
    CalendarCellTrigger,
    CalendarGrid,
    CalendarGridBody,
    CalendarGridHead,
    CalendarGridRow,
    CalendarHeadCell,
    CalendarHeader,
    CalendarHeading,
    CalendarNext,
    CalendarPrev,
    CalendarRoot,
    PopoverContent,
    PopoverPortal,
    PopoverRoot,
    PopoverTrigger,
    type DateValue,
} from 'reka-ui';
import { computed, ref, watch } from 'vue';

const props = withDefaults(
    defineProps<{
        modelValue?: string | null;
        placeholder?: string;
        id?: string;
        disabled?: boolean;
        invalid?: boolean;
        clearable?: boolean;
        withTime?: boolean;
    }>(),
    {
        modelValue: null,
        placeholder: 'Select a date',
        disabled: false,
        invalid: false,
        clearable: false,
        withTime: false,
    },
);

const emit = defineEmits<{
    'update:modelValue': [value: string | null];
}>();

const open = ref(false);
const placeholderDate = ref<DateValue>(asDateValue(toCalendarDate(props.modelValue) ?? today(getLocalTimeZone())));

const selectedDate = computed(() => {
    const parsed = toCalendarDate(props.modelValue);
    return parsed ? asDateValue(parsed) : undefined;
});

const calendarDate = computed(() => selectedDate.value as never);
const calendarPlaceholder = computed({
    get: () => placeholderDate.value as never,
    set: (value) => {
        if (value) {
            placeholderDate.value = value as DateValue;
        }
    },
});
const selectedTime = computed(() => timePart(props.modelValue));

const label = computed(() => {
    const date = selectedDate.value;
    if (!date) {
        return '';
    }

    const formatted = date.toDate(getLocalTimeZone()).toLocaleDateString('en-GB', {
        day: 'numeric',
        month: 'short',
        year: 'numeric',
    });

    return props.withTime ? `${formatted}, ${selectedTime.value}` : formatted;
});

watch(
    () => props.modelValue,
    (value) => {
        const parsed = toCalendarDate(value);
        if (parsed) {
            placeholderDate.value = asDateValue(parsed);
        }
    },
);

function asDateValue(value: CalendarDate): DateValue {
    return value as unknown as DateValue;
}

function toCalendarDate(value: string | null | undefined): CalendarDate | undefined {
    const match = value?.match(/^(\d{4})-(\d{2})-(\d{2})/);
    if (!match) {
        return undefined;
    }

    return new CalendarDate(Number(match[1]), Number(match[2]), Number(match[3]));
}

function timePart(value: string | null | undefined): string {
    const match = value?.match(/T(\d{2}:\d{2})/);
    return match?.[1] ?? '00:00';
}

function onDate(value: DateValue | undefined) {
    if (!value) {
        return;
    }

    const date = value.toString().slice(0, 10);

    if (!props.withTime) {
        emit('update:modelValue', date);
        open.value = false;
        return;
    }

    emit('update:modelValue', `${date}T${selectedTime.value}`);
}

function onTime(event: Event) {
    const time = (event.target as HTMLInputElement).value || '00:00';
    const date = selectedDate.value?.toString().slice(0, 10) ?? today(getLocalTimeZone()).toString();
    emit('update:modelValue', `${date}T${time}`);
}

function selectToday() {
    onDate(asDateValue(today(getLocalTimeZone())));
}

function clear() {
    emit('update:modelValue', null);
    open.value = false;
}
</script>

<template>
    <PopoverRoot v-model:open="open">
        <div class="relative">
            <PopoverTrigger as-child>
                <button
                    :id="id"
                    type="button"
                    :aria-invalid="invalid || undefined"
                    :disabled="disabled"
                    :class="
                        cn(
                            'flex h-10 w-full items-center justify-between gap-2 rounded-xl border border-input bg-background px-3 text-left text-sm shadow-xs transition-colors outline-none',
                            'focus-visible:border-ring focus-visible:ring-[3px] focus-visible:ring-ring/50',
                            'disabled:cursor-not-allowed disabled:opacity-50',
                            invalid && 'border-destructive ring-destructive/20',
                            clearable && modelValue ? 'pr-16' : '',
                        )
                    "
                >
                    <span class="truncate" :class="label ? 'text-foreground' : 'text-muted-foreground'">
                        {{ label || placeholder }}
                    </span>
                    <Icon name="calendar" class="size-4 shrink-0 text-muted-foreground" />
                </button>
            </PopoverTrigger>
            <button
                v-if="clearable && modelValue"
                type="button"
                class="absolute top-1/2 right-9 flex size-6 -translate-y-1/2 items-center justify-center rounded-md text-muted-foreground hover:bg-muted hover:text-foreground"
                aria-label="Clear date"
                @click="clear"
            >
                <Icon name="x" class="size-3.5" />
            </button>
        </div>

        <PopoverPortal>
            <PopoverContent
                align="start"
                :side-offset="6"
                :collision-padding="8"
                class="z-50 w-[19rem] rounded-xl border bg-popover p-3 text-popover-foreground shadow-lg"
            >
                <CalendarRoot
                    v-slot="{ grid, weekDays }"
                    :model-value="calendarDate"
                    v-model:placeholder="calendarPlaceholder"
                    :prevent-deselect="true"
                    fixed-weeks
                    locale="en-US"
                    weekday-format="short"
                    @update:model-value="onDate"
                >
                    <CalendarHeader class="flex items-center justify-between">
                        <CalendarPrev
                            class="inline-flex size-8 items-center justify-center rounded-lg text-muted-foreground hover:bg-accent hover:text-foreground"
                            aria-label="Previous month"
                        >
                            <Icon name="chevronLeft" class="size-4" />
                        </CalendarPrev>
                        <CalendarHeading class="text-sm font-medium" />
                        <CalendarNext
                            class="inline-flex size-8 items-center justify-center rounded-lg text-muted-foreground hover:bg-accent hover:text-foreground"
                            aria-label="Next month"
                        >
                            <Icon name="chevronRight" class="size-4" />
                        </CalendarNext>
                    </CalendarHeader>

                    <div class="mt-3 flex flex-col gap-y-4">
                        <CalendarGrid v-for="month in grid" :key="month.value.toString()" class="w-full border-collapse select-none">
                            <CalendarGridHead>
                                <CalendarGridRow class="flex">
                                    <CalendarHeadCell
                                        v-for="day in weekDays"
                                        :key="day"
                                        class="w-9 text-center text-[0.7rem] font-medium text-muted-foreground"
                                    >
                                        {{ day }}
                                    </CalendarHeadCell>
                                </CalendarGridRow>
                            </CalendarGridHead>
                            <CalendarGridBody>
                                <CalendarGridRow v-for="(weekDates, index) in month.rows" :key="`${month.value}-${index}`" class="mt-1 flex w-full">
                                    <CalendarCell
                                        v-for="weekDate in weekDates"
                                        :key="weekDate.toString()"
                                        :date="weekDate"
                                        class="relative size-9 p-0 text-center"
                                    >
                                        <CalendarCellTrigger
                                            :day="weekDate"
                                            :month="month.value"
                                            class="inline-flex size-9 items-center justify-center rounded-lg text-sm outline-none hover:bg-accent focus-visible:ring-2 focus-visible:ring-ring data-[disabled]:pointer-events-none data-[disabled]:opacity-30 data-[outside-view]:text-muted-foreground/40 data-[selected]:bg-primary data-[selected]:text-primary-foreground data-[today]:ring-1 data-[today]:ring-border"
                                        />
                                    </CalendarCell>
                                </CalendarGridRow>
                            </CalendarGridBody>
                        </CalendarGrid>
                    </div>
                </CalendarRoot>

                <div v-if="withTime" class="mt-3 border-t pt-3">
                    <label class="mb-1.5 block text-xs font-medium text-muted-foreground" :for="id ? `${id}-time` : undefined">Time</label>
                    <input
                        :id="id ? `${id}-time` : undefined"
                        type="time"
                        :value="selectedTime"
                        class="h-9 w-full rounded-lg border border-input bg-background px-3 text-sm outline-none focus-visible:border-ring focus-visible:ring-[3px] focus-visible:ring-ring/50"
                        @input="onTime"
                    />
                </div>

                <div class="mt-3 flex items-center justify-between border-t pt-3">
                    <button type="button" class="text-sm font-medium text-primary hover:underline" @click="selectToday">Today</button>
                    <button
                        type="button"
                        class="rounded-lg px-2 py-1 text-sm text-muted-foreground hover:bg-accent hover:text-foreground"
                        @click="open = false"
                    >
                        Done
                    </button>
                </div>
            </PopoverContent>
        </PopoverPortal>
    </PopoverRoot>
</template>
