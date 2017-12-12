export module Variable {
    export function isNullOrUndefined(object: any): boolean {
        return object === null || object === undefined
    }
    export function isNotNullOrUndefined(object: any): boolean {
        return !Variable.isNullOrUndefined(object);
    }
    export function isEmptyString(object: any): boolean {
        return object === '';
    }
    export function isNullOrUndefinedOrEmptyString(object: any): boolean {
        return Variable.isNullOrUndefined(object) || Variable.isEmptyString(object);
    }
    export function isNotNullOrUndefinedOrEmptyString(object: any): boolean {
        return !Variable.isNullOrUndefinedOrEmptyString(object);
    }
    export function isFunction(functionToCheck: any): boolean {
        return functionToCheck && ({}).toString.call(functionToCheck) === '[object Function]';
    }
}