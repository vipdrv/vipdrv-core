import { LogLevel } from "./log-level";

export interface ILogger {
    isEnabled(logLevel: LogLevel): boolean;

    enableAll(): void;
    enable(...logLevels: Array<LogLevel>): void;

    disableAll(): void;
    disable(...logLevels: Array<LogLevel>): void;

    logMessage(logLevel: LogLevel, message: string): void;

    logTrase(message: string): void;
    logDebug(message: string): void;
    logInformation(message: string): void;
    logWarning(message: string): void;
    logError(message: string): void;
    logCritical(message: string): void;
}