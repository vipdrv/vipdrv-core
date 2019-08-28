import { Injectable } from '@angular/core';
import { ILogger } from './../../utils/index';
import { IValidationService } from './i-validation-service';
@Injectable()
export abstract class ObjectValidationService<TObject> implements IValidationService<TObject> {
    /// injected dependencies
    protected logger: ILogger;
    /// ctor
    constructor(logger: ILogger) {
        this.logger = logger;
    }
    /// methods
    abstract isValid(entity: TObject): boolean;
}