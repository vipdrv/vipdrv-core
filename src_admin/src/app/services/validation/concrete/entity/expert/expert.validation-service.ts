import { Injectable } from '@angular/core';
import { Variable, ConsoleLogger } from './../../../../../utils/index';
import { ExpertEntity } from './../../../../../entities/index';
import { ObjectValidationService } from './../../../object.validation-service';
import { IExpertValidationService } from './i-expert.validation-service';
@Injectable()
export class ExpertValidationService
    extends ObjectValidationService<ExpertEntity>
    implements IExpertValidationService {
    /// ctor
    constructor(logger: ConsoleLogger) {
        super(logger);
        this.logger.logDebug('ExpertValidationService: Service has been constructed.');
    }
    /// methods
    isValid(entity: ExpertEntity): boolean {
        return Variable.isNotNullOrUndefined(entity) &&
            this.isValidName(entity) &&
            this.isValidDescription(entity) &&
            this.isValidLinkedInUrl(entity) &&
            this.isValidFacebookUrl(entity);
    }
    isValidName(entity: ExpertEntity): boolean {
        return Variable.isNotNullOrUndefined(entity) &&
            Variable.isNotNullOrUndefined(entity.name);
    }
    isValidDescription(entity: ExpertEntity): boolean {
        return Variable.isNotNullOrUndefined(entity) &&
            Variable.isNotNullOrUndefined(entity.description);
    }
    isValidLinkedInUrl(entity: ExpertEntity): boolean {
        return Variable.isNotNullOrUndefined(entity) &&
            (
                Variable.isNullOrUndefined(entity.linkedinUrl) ||
                entity.linkedinUrl.startsWith('http://www.linkedin.com')
            );
    }
    isValidFacebookUrl(entity: ExpertEntity): boolean {
        return Variable.isNotNullOrUndefined(entity) &&
            (
                Variable.isNullOrUndefined(entity.facebookUrl) ||
                entity.facebookUrl.startsWith('http://www.facebook.com')
            );
    }
    getInvalidNameMessageKey(entity: ExpertEntity): string {
        return 'validation.experts.name';
    }
    getInvalidDescriptionMessageKey(entity: ExpertEntity): string {
        return 'validation.experts.description';
    }
    getInvalidLinkedInUrlMessageKey(entity: ExpertEntity): string {
        return 'validation.experts.linkedInUrl';
    }
    getInvalidFacebookUrlMessageKey(entity: ExpertEntity): string {
        return 'validation.experts.facebookUrl';
    }
}