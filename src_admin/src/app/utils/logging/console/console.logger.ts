﻿import { Injectable } from "@angular/core";
import { ILogger } from "./../i-logger";
import { BaseLogger } from "./../base.logger";
import { LogLevel } from "./../log-level";
import { environment } from '../../../../environments/environment';
@Injectable()
export class ConsoleLogger extends BaseLogger implements ILogger {
    /// ctor
    constructor() {
        super();

        if (environment && environment.enabeLogging) {
            this.enableAll();
        } else {
            this.disableAll();
        }
    }
    /// methods
    logTrase(message: string): void {
        this.logMessageUsingConsole(
            this.getCurrentTime() + " - " +
            LogLevel.getName(LogLevel.Trace) + " - " +
            message
        );
    }

    logDebug(message: string): void {
        this.logMessageUsingConsole(
            this.getCurrentTime() + " - " +
            LogLevel.getName(LogLevel.Debug) + " - " +
            message
        );
    }

    logInformation(message: string): void {
        this.logMessageUsingConsole(
            this.getCurrentTime() + " - " +
            LogLevel.getName(LogLevel.Information) + " - " +
            message
        );
    }

    logWarning(message: string): void {
        this.logMessageUsingConsole(
            this.getCurrentTime() + " - " +
            LogLevel.getName(LogLevel.Warning) + " - " +
            message
        );
    }

    logError(message: string): void {
        this.logMessageUsingConsole(
            this.getCurrentTime() + " - " +
            LogLevel.getName(LogLevel.Error) + " - " +
            message
        );
    }

    logCritical(message: string): void {
        this.logMessageUsingConsole(
            this.getCurrentTime() + " - " +
            LogLevel.getName(LogLevel.Critical) + " - " +
            message
        );
    }

    /// helpers

    protected logMessageUsingConsole(msg: string) {
        if (console) {
            console.log(msg);
        }
    }

    protected getCurrentTime(): string {
        return new Date().toLocaleString();
    }
}