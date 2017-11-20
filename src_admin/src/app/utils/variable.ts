export module Variable {
    export function isNullOrUndefined(objects: any): boolean {
        if (objects === null || objects === undefined) {
            return true;
        }
    }
    export function isNotNullOrUndefined(objects: any): boolean {
        return !Variable.isNullOrUndefined(objects);
    }
    export function isFunction(functionToCheck: any): boolean {
        let getType = {};
        return functionToCheck && getType.toString.call(functionToCheck) === '[object Function]';
    }
}