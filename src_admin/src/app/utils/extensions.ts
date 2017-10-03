import { DatePipe } from '@angular/common';
import { Variable } from './variable';
export module Extensions {
    /// regular expressions
    export const regExp = {
        email: /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/,
        phoneNumber: /^\d\d\d\d\d\d\d\d\d\d\d\d$/,
        // in format HH:mm:ss
        time: /^(?:2[0-3]|[01][0-9]):[0-5][0-9]:[0-5][0-9]$/
    };
    /// date time extensions
    export const dateTimeLocale: string = 'en-US';
    export const dateTimePattern: string = ' MM/dd/yyyy HH:mm:ss';
    export function formatDateTime(dateTime: string): string {
        if (Variable.isNotNullOrUndefined(dateTime)) {
            return new DatePipe(dateTimeLocale).transform(dateTime, dateTimePattern);
        } else {
            throw new Error('Argument (dateTime) exceprion!');
        }
    }
    export function formatUtcDateTimeToLocalTimezone(dateTime: string): string {
        if (Variable.isNotNullOrUndefined(dateTime)) {
            let res =  formatDateTime(
                new Date((new Date(dateTime)).getTime() - (60 * 1000 * new Date().getTimezoneOffset()))
                    .toString());
            return res;
        } else {
            throw new Error('Argument (dateTime) exceprion!');
        }
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