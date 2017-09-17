import { DatePipe } from '@angular/common';
import { Variable } from './variable';
export module Extensions {
    /// date time extensions
    const dateTimeLocale: string = 'en-US';
    const dateTimePattern: string = ' MM/dd/yyyy HH:mm:ss';
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
}