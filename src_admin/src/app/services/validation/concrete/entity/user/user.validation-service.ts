import { Injectable } from '@angular/core';
import { ConsoleLogger } from '../../../../../utils/logging/console/console.logger';
import { ObjectValidationService } from '../../../object.validation-service';
import { UserEntity } from '../../../../../entities/main/users/user.entity';
import { IUserValidationService } from './i-user.validation-service';
import { Variable } from '../../../../../utils/variable';
import { variable } from '@angular/compiler/src/output/output_ast';
import { Extensions } from '../../../../../utils/extensions';

@Injectable()
export class UserValidationService
    extends ObjectValidationService<UserEntity>
    implements IUserValidationService {

    /// ctor
    constructor(logger: ConsoleLogger) {
        super(logger);
        this.logger.logDebug('ExpertValidationService: Service has been constructed.');
    }

    /// methods
    isValid(entity: UserEntity): boolean {
        return Variable.isNotNullOrUndefined(entity) &&
            this.isValidUserName(entity) &&
            this.isValidUserEmail(entity) &&
            this.isValidUserFirstName(entity) &&
            this.isValidUserSecondName(entity) &&
            this.isValidUserPhone(entity) &&
            this.isValidUserSitesCount(entity);
    }

    isValidUserName(entity: UserEntity): boolean {
        return Variable.isNotNullOrUndefined(entity) &&
            Variable.isNotNullOrUndefinedOrEmptyString(entity.username);
    }

    isValidUserPassword(entity: UserEntity): boolean {
        return Variable.isNotNullOrUndefined(entity) &&
            Variable.isNotNullOrUndefinedOrEmptyString(entity.password);
    }

    isValidUserPasswordRepeat(entity: UserEntity, repeatPassword: string) {
        return Variable.isNotNullOrUndefined(entity) &&
            Variable.isNotNullOrUndefinedOrEmptyString(entity.password) &&
            entity.password === repeatPassword;
    }

    isValidUserPasswordReset(password: string) {
        return true;
    }

    isValidUserEmail(entity: UserEntity): boolean {
        return Variable.isNotNullOrUndefined(entity) && (
            Variable.isNotNullOrUndefinedOrEmptyString(entity.email) &&
            Extensions.regExp.email.test(entity.email));
    }

    isValidUserFirstName(entity: UserEntity): boolean {
        return Variable.isNotNullOrUndefined(entity) &&
            Variable.isNotNullOrUndefinedOrEmptyString(entity.firstName);
    }

    isValidUserSecondName(entity: UserEntity): boolean {
        return Variable.isNotNullOrUndefined(entity);
    }

    isValidUserPhone(entity: UserEntity): boolean {
        return Variable.isNotNullOrUndefined(entity) && (
            !Variable.isNotNullOrUndefinedOrEmptyString(entity.phoneNumber) ||
            Extensions.regExp.phoneNumber.test(entity.phoneNumber)
        );
    }

    isValidUserSitesCount(entity: UserEntity): boolean {
        return Variable.isNotNullOrUndefined(entity) &&
            Variable.isNotNullOrUndefinedOrEmptyString(entity.maxSitesCount) &&
            Number.isInteger(entity.maxSitesCount);
    }

    getInvalidUserNameMessageKey(entity: UserEntity): string {
        return 'validation.registrationModel.invalidUsernameMessage';
    }

    getInvalidUserPasswordMessageKey(entity: UserEntity): string {
        return 'validation.registrationModel.invalidPasswordMessage';
    }

    getInvalidUserRepeatPasswordMessageKey(entity: UserEntity) {
        return 'validation.registrationModel.invalidRepeatPasswordMessage';
    }

    getInvalidUserEmailMessageKey(entity: UserEntity): string {
        return 'validation.registrationModel.invalidEmailMessage';
    }

    getInvalidUserFirstNameMessageKey(entity: UserEntity): string {
        return 'validation.registrationModel.invalidFirstNameMessage';
    }

    getInvalidUserSecondNameMessageKey(entity: UserEntity): string {
        return 'validation.registrationModel.invalidSecondNameMessage';
    }

    getInvalidUserPhoneMessageKey(entity: UserEntity): string {
        return 'validation.registrationModel.invalidPhoneNumberMessage';
    }

    getInvalidUserSitesCountMessageKey(entity: UserEntity): string {
        return 'validation.users.invalidSitesCountMessage';
    }
}
