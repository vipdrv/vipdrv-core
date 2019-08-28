import { Injectable } from '@angular/core';
import { Extensions, Variable, ConsoleLogger } from './../../../../../utils/index';
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
            this.isValidTitle(entity) &&
            this.isValidDescription(entity) &&
            this.isValidEmail(entity) &&
            this.isValidPhoneNumber(entity) &&
            this.isValidLinkedInUrl(entity) &&
            this.isValidFacebookUrl(entity);
    }
    isValidName(entity: ExpertEntity): boolean {
        return Variable.isNotNullOrUndefined(entity) &&
            Variable.isNotNullOrUndefinedOrEmptyString(entity.name);
    }
    isValidTitle(entity: ExpertEntity): boolean {
        return Variable.isNotNullOrUndefined(entity) &&
            Variable.isNotNullOrUndefinedOrEmptyString(entity.title);
    }
    isValidDescription(entity: ExpertEntity): boolean {
        return Variable.isNotNullOrUndefined(entity);
    }
    isValidEmail(entity: ExpertEntity): boolean {
        return Variable.isNotNullOrUndefined(entity) &&
            (
                !Variable.isNotNullOrUndefinedOrEmptyString(entity.email) ||
                Extensions.regExp.email.test(entity.email)
            );
    }
    isValidPhoneNumber(entity: ExpertEntity): boolean {
        return Variable.isNotNullOrUndefined(entity) &&
            (
                !Variable.isNotNullOrUndefinedOrEmptyString(entity.phoneNumber) ||
                Extensions.regExp.phoneNumber.test(entity.phoneNumber)
            );
    }
    isValidLinkedInUrl(entity: ExpertEntity): boolean {
        return Variable.isNotNullOrUndefined(entity) &&
            (
                !Variable.isNotNullOrUndefinedOrEmptyString(entity.linkedinUrl) ||
                entity.linkedinUrl.startsWith('https://www.linkedin.com')
            );
    }
    isValidDealerraterUrl(entity: ExpertEntity): boolean {
        return Variable.isNotNullOrUndefined(entity) &&
            (
                !Variable.isNotNullOrUndefinedOrEmptyString(entity.dealerraterUrl) ||
                entity.dealerraterUrl.startsWith('https://www.dealerrater.com')
            );
    }
    isValidFacebookUrl(entity: ExpertEntity): boolean {
        return Variable.isNotNullOrUndefined(entity) &&
            (
                !Variable.isNotNullOrUndefinedOrEmptyString(entity.facebookUrl) ||
                entity.facebookUrl.startsWith('https://www.facebook.com')
            );
    }
    isValidEmployeeId(entity: ExpertEntity): boolean {
        return Variable.isNotNullOrUndefined(entity);
    }
    getInvalidNameMessageKey(entity: ExpertEntity): string {
        return 'validation.experts.name';
    }
    getInvalidTitleMessageKey(entity: ExpertEntity): string {
        return 'validation.experts.title';
    }
    getInvalidDescriptionMessageKey(entity: ExpertEntity): string {
        return 'validation.experts.description';
    }
    getInvalidEmailMessageKey(entity: ExpertEntity): string {
        return 'validation.experts.email';
    }
    getInvalidPhoneNumberMessageKey(entity: ExpertEntity): string {
        return 'validation.experts.phoneNumber';
    }
    getInvalidLinkedInUrlMessageKey(entity: ExpertEntity): string {
        return 'validation.experts.linkedInUrl';
    }
    getInvalidDealerraterUrlMessageKey(entity: ExpertEntity): string {
        return 'validation.experts.dealerraterUrl';
    }
    getInvalidFacebookUrlMessageKey(entity: ExpertEntity): string {
        return 'validation.experts.facebookUrl';
    }
    getInvalidEmployeeIdMessageKey(entity: ExpertEntity): string {
        return 'validation.experts.employeeId';
    }
}