import { DatePipe } from '@angular/common';
import { Variable } from './variable';
export namespace Extensions {
    /// regular expressions
    export const regExp = {
        // language=JSRegexp
        email: /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/,
        phoneNumber: /^\d\d\d\d\d\d\d\d\d\d\d\d$/,
        // in format HH:mm:ss
        time: /^(?:2[0-3]|[01][0-9]):[0-5][0-9]:[0-5][0-9]$/
    };
    /// date time extensions
    export const dateTimeLocale: string = 'en-US';
    export const dateTimePattern: string = 'MM/dd/yyyy HH:mm';
    export const serverDateTimePattern: string = 'yyyy-MM-dd HH:mm';
    export function formatDateTime(dateTime: string): string {
        if (Variable.isNotNullOrUndefined(dateTime)) {
            return new DatePipe(dateTimeLocale).transform(dateTime, dateTimePattern);
        } else {
            throw new Error('Argument (dateTime) exceprion!');
        }
    }
    export function formatUtcDateTimeToLocalTimezone(dateTime: string): string {
        if (Variable.isNotNullOrUndefined(dateTime)) {
            const res = formatDateTime(
                new Date((new Date(dateTime)).getTime() - (60 * 1000 * new Date().getTimezoneOffset()))
                    .toString());
            return res;
        } else {
            throw new Error('Argument (dateTime) exceprion!');
        }
    }
    export function todayValue(): string {
        const date = new Date();
        const dateWithoutTimezone = new Date(
            date.getUTCFullYear(),
            date.getUTCMonth(),
            date.getUTCDate());
        return new DatePipe(dateTimeLocale).transform(dateWithoutTimezone, serverDateTimePattern);
    }
    export function lastWeekValue(): string {
        const date = new Date();
        date.setDate(date.getDate() - 7);
        const dateWithoutTimezone = new Date(
            date.getUTCFullYear(),
            date.getUTCMonth(),
            date.getUTCDate());
        return new DatePipe(dateTimeLocale).transform(dateWithoutTimezone, serverDateTimePattern);
    }
    export function thisMonthValue(): string {
        const date = new Date();
        const dateWithoutTimezone = new Date(
            date.getUTCFullYear(),
            date.getUTCMonth());
        return new DatePipe(dateTimeLocale).transform(dateWithoutTimezone, serverDateTimePattern);
    }
    export function lastMonthValue(): string {
        const date = new Date();
        date.setMonth(date.getMonth() - 1);
        const dateWithoutTimezone = new Date(
            date.getUTCFullYear(),
            date.getUTCMonth(),
            date.getUTCDate());
        return new DatePipe(dateTimeLocale).transform(dateWithoutTimezone, serverDateTimePattern);
    }
    /// validation extensions
    export function getInputValidationClass(isValidationActive: boolean, isValueValid: boolean): any {
        return {
            'test-drive-valid-input': isValidationActive && isValueValid,
            'test-drive-invalid-input': isValidationActive && !isValueValid
        };
    }
    /// promises extensions
    export function delay(timeout) {
        return new Promise(function(resolve) {
            setTimeout(resolve, timeout)
        });
    }
    /// random extensions
    export function generateGuid() {
        return this.generateS4() + this.generateS4() + '-' +
            this.generateS4() + '-' +
            this.generateS4() + '-' +
            this.generateS4() + '-' +
            this.generateS4() + this.generateS4() + this.generateS4();
    }
    export function generateS4() {
        return Math.floor((1 + Math.random()) * 0x10000)
            .toString(16)
            .substring(1);
    }
}