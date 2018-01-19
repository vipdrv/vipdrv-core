import { Component, Input } from '@angular/core';
import { UserEntity } from '../../../../entities/main/users/user.entity';
import { ConsoleLogger } from '../../../../utils/logging/console/console.logger';
import { ILogger } from '../../../../utils/logging/i-logger';
import { IUserValidationService } from '../../../../services/validation/concrete/entity/user/i-user.validation-service';
import { Extensions } from '../../../../utils/extensions';
import { UserValidationService } from 'app/services';

@Component({
    selector: 'user-details-edit',
    templateUrl: './userDetailsEdit.html'
})
export class UserDetailsEditComponent {
    /// inputs
    @Input() entity: UserEntity;
    @Input() isReadOnly: boolean = false;
    @Input() useValidation: boolean = false;
    @Input() isModalInEditMode: boolean = false;
    /// outputs
    /// fields
    protected usaPhoneMask = Extensions.masks.usaPhoneMask;
    protected isPasswordGeneratorVisible: boolean = false;
    /// injected dependencies
    protected extensions = Extensions;
    protected logger: ILogger;
    protected validationService: IUserValidationService;

    /// ctor
    constructor(logger: ConsoleLogger, validationService: UserValidationService) {
        this.logger = logger;
        this.validationService = validationService;
        this.logger.logDebug('ExpertDetailsEditComponent: Component has been constructed.');
    }

    /// methods

    protected showPasswordGenerator(): void {
        this.entity.password = this.generatePassword();
        this.isPasswordGeneratorVisible = true;
    }


    protected hidePasswordGenerator(): void {
        this.entity.password = null;
        this.isPasswordGeneratorVisible = false;
    }

    protected generatePassword(): string {
        return Math.random().toString(12).slice(2);
    }


    /// predicates
    protected isValidationActive(): boolean {
        return this.useValidation;
    }

    protected isComponentReadOnly(): boolean {
        return this.isReadOnly;
    }
}

