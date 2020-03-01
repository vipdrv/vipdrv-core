export enum LogLevel {
    Trace = 0,
    Debug = 1,
    Information = 2,
    Warning = 3,
    Error = 4,
    Critical = 5
}
export namespace LogLevel {
    export function getName(arg: LogLevel): string {
        switch (arg) {
            case LogLevel.Trace:
                return "Trace";
            case LogLevel.Debug:
                return "Debug";
            case LogLevel.Information:
                return "Information";
            case LogLevel.Warning:
                return "Warning";
            case LogLevel.Error:
                return "Error";
            case LogLevel.Critical:
                return "Critical";
            default:
                throw new Error(
                    "Argument exception! Not supported LogLevel (" + arg + ").");
        }
    }

    export function getAll(): LogLevel[] {
        return [
            LogLevel.Trace,
            LogLevel.Debug,
            LogLevel.Information,
            LogLevel.Warning,
            LogLevel.Error,
            LogLevel.Critical
        ];
    }
}