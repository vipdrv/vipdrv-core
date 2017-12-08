import { Injectable } from '@angular/core';
import { Variable, Extensions, ConsoleLogger } from './../../../../utils/index';
import { RegistrationModelEntity } from './../../../../entities/index';
import { ObjectValidationService } from './../../object.validation-service';
import { IRegistrationModelValidationService } from './i-RegistrationModel.validation-service';
@Injectable()
export class RegistrationModelValidationService
    extends ObjectValidationService<RegistrationModelEntity>
    implements IRegistrationModelValidationService {
    /// ctor
    constructor(logger: ConsoleLogger) {
        super(logger);
        this.logger.logDebug('RegistrationModelValidationService: Service has been constructed.');
    }
    /// methods
    isValid(entity: RegistrationModelEntity): boolean {
        return this.isValidEmail(entity) &&
            this.isValidEmail(entity) &&
            this.isValidFirstName(entity) &&
            this.isValidSecondName(entity) &&
            this.isValidPassword(entity) &&
            this.isValidRepeatPassword(entity) &&
            this.isValidAvatarUrl(entity) &&
            this.isValidPhoneNumber(entity);
    }
    isValidEmail(entity: RegistrationModelEntity): boolean {
        return Variable.isNotNullOrUndefined(entity) && Extensions.regExp.email.test(entity.email);
    }
    isValidFirstName(entity: RegistrationModelEntity): boolean {
        return Variable.isNotNullOrUndefined(entity) && Variable.isNotNullOrUndefined(entity.firstName);
    }
    isValidSecondName(entity: RegistrationModelEntity): boolean {
        return Variable.isNotNullOrUndefined(entity) && Variable.isNotNullOrUndefined(entity.secondName);
    }
    isValidPassword(entity: RegistrationModelEntity): boolean {
        return Variable.isNotNullOrUndefined(entity) && Variable.isNotNullOrUndefined(entity.password);
    }
    isValidRepeatPassword(entity: RegistrationModelEntity): boolean {
        return Variable.isNotNullOrUndefined(entity) && entity.repeatPassword == entity.password;
    }
    isValidAvatarUrl(entity: RegistrationModelEntity): boolean {
        return Variable.isNotNullOrUndefined(entity) && Variable.isNotNullOrUndefined(entity.avatarUrl);
    }
    isValidPhoneNumber(entity: RegistrationModelEntity): boolean {
        return Variable.isNotNullOrUndefined(entity) &&
            (
                Variable.isNullOrUndefined(entity.phoneNumber) ||
                Extensions.regExp.phoneNumber.test(entity.phoneNumber)
            );
    }

    getInvalidEmailMessageKey(site: RegistrationModelEntity): string {
        return 'validation.registrationModel.invalidEmailMessage';
    }
    getInvalidFirstNameMessageKey(site: RegistrationModelEntity): string {
        return 'validation.registrationModel.invalidFirstNameMessage';
    }
    getInvalidSecondNameMessageKey(site: RegistrationModelEntity): string {
        return 'validation.registrationModel.invalidSecondNameMessage';
    }
    getInvalidPasswordMessageKey(site: RegistrationModelEntity): string {
        return 'validation.registrationModel.invalidPasswordMessage';
    }
    getInvalidRepeatPasswordMessageKey(site: RegistrationModelEntity): string {
        return 'validation.registrationModel.invalidRepeatPasswordMessage';
    }
    getInvalidAvatarUrlMessageKey(site: RegistrationModelEntity): string {
        return 'validation.registrationModel.invalidAvatarUrlMessage';
    }
    getInvalidPhoneNumberMessageKey(site: RegistrationModelEntity): string {
        return 'validation.registrationModel.invalidPhoneNumberMessage';
    }
}