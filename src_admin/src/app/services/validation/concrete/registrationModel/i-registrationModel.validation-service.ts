import { RegistrationModelEntity } from './../../../../entities/index';
import { IValidationService } from './../../i-validation-service';
export interface IRegistrationModelValidationService extends IValidationService<RegistrationModelEntity> {
    isValidEmail(entity: RegistrationModelEntity): boolean;
    isValidFirstName(entity: RegistrationModelEntity): boolean;
    isValidSecondName(entity: RegistrationModelEntity): boolean;
    isValidPassword(entity: RegistrationModelEntity): boolean;
    isValidRepeatPassword(entity: RegistrationModelEntity): boolean;
    isValidAvatarUrl(entity: RegistrationModelEntity): boolean;
    isValidPhoneNumber(entity: RegistrationModelEntity): boolean;

    getInvalidEmailMessageKey(site: RegistrationModelEntity): string;
    getInvalidFirstNameMessageKey(site: RegistrationModelEntity): string;
    getInvalidSecondNameMessageKey(site: RegistrationModelEntity): string;
    getInvalidPasswordMessageKey(site: RegistrationModelEntity): string;
    getInvalidRepeatPasswordMessageKey(site: RegistrationModelEntity): string;
    getInvalidAvatarUrlMessageKey(site: RegistrationModelEntity): string;
    getInvalidPhoneNumberMessageKey(site: RegistrationModelEntity): string;
}