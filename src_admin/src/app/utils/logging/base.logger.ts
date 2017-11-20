import { ILogger } from "./i-logger";
import { LogLevel } from "./log-level";
import { IStringKeyedCollection } from "./../keyed-collection/i-string-keyed-collection";
import { StringKeyedCollection } from "./../keyed-collection/string-keyed-collection";

export abstract class BaseLogger implements ILogger {
    private _logLevelStateDictionary: IStringKeyedCollection<boolean>;
    protected get logLevelStateDictionary(): IStringKeyedCollection<boolean> {
        return this._logLevelStateDictionary;
    }

    constructor() {
        this.initializeLogLevelStateDictionary();
    }

    isEnabled(logLevel: LogLevel): boolean {
        return this.logLevelStateDictionary.containsKey(LogLevel.getName(logLevel)) &&
            this.logLevelStateDictionary.item(LogLevel.getName(logLevel));
    }

    enableAll(): void {
        this.enable(
            LogLevel.Trace,
            LogLevel.Debug,
            LogLevel.Information,
            LogLevel.Warning,
            LogLevel.Error,
            LogLevel.Critical
        );
    }

    enable(...logLevels: Array<LogLevel>): void {
        let self: BaseLogger = this;
        logLevels.forEach(
            function (
                value: LogLevel,
                index: number,
                array: Array<LogLevel>
            ): void {
                self.changeLogLevelState(value, true);
            }
        );
    }

    disableAll(): void {
        this.disable(
            LogLevel.Trace,
            LogLevel.Debug,
            LogLevel.Information,
            LogLevel.Warning,
            LogLevel.Error,
            LogLevel.Critical
        );
    }

    disable(...logLevels: Array<LogLevel>): void {
        let self: BaseLogger = this;
        logLevels.forEach(
            function (
                value: LogLevel,
                index: number,
                array: Array<LogLevel>
            ): void {
                self.changeLogLevelState(value, false);
            }
        );
    }

    logMessage(logLevel: LogLevel, message: string): void {
        switch (logLevel) {
            case LogLevel.Trace:
                return this.logTrase(message);
            case LogLevel.Debug:
                return this.logDebug(message);
            case LogLevel.Information:
                return this.logInformation(message);
            case LogLevel.Warning:
                return this.logWarning(message);
            case LogLevel.Error:
                return this.logError(message);
            case LogLevel.Critical:
                return this.logCritical(message);
            default:
                throw new Error(
                    "Argument exception! Not supported LogLevel (" + logLevel + ").");
        }
    }

    abstract logTrase(message: string): void;
    abstract logDebug(message: string): void;
    abstract logInformation(message: string): void;
    abstract logWarning(message: string): void;
    abstract logError(message: string): void;
    abstract logCritical(message: string): void;

    /// helpers

    private initializeLogLevelStateDictionary(): void {
        this._logLevelStateDictionary = new StringKeyedCollection<boolean>();
        this.logLevelStateDictionary.add(LogLevel.getName(LogLevel.Trace), false);
        this.logLevelStateDictionary.add(LogLevel.getName(LogLevel.Debug), false);
        this.logLevelStateDictionary.add(LogLevel.getName(LogLevel.Information), false);
        this.logLevelStateDictionary.add(LogLevel.getName(LogLevel.Warning), false);
        this.logLevelStateDictionary.add(LogLevel.getName(LogLevel.Error), false);
        this.logLevelStateDictionary.add(LogLevel.getName(LogLevel.Critical), false);
    }

    private changeLogLevelState(logLevel: LogLevel, newState: boolean): void {
        this._logLevelStateDictionary
            .addOrUpdate(
                LogLevel.getName(logLevel),
                true,
                function (key: string, value: boolean): boolean { return true; }
            );
    }
}