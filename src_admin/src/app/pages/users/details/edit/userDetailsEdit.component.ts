import { Component, Input } from '@angular/core';
import { UserEntity } from '../../../../entities/main/users/user.entity';
import { ConsoleLogger } from '../../../../utils/logging/console/console.logger';
import { ExpertValidationService } from '../../../../services/validation/concrete/entity/expert/expert.validation-service';
import { ILogger } from '../../../../utils/logging/i-logger';
import { IUserValidationService } from '../../../../services/validation/concrete/entity/user/i-user.validation-service';

@Component({
    selector: 'user-details-edit',

    templateUrl: './userDetailsEdit.html'
})
export class UserDetailsEditComponent {
    /// inputs
    @Input() entity: UserEntity;
    @Input() isReadOnly: boolean = false;
    @Input() useValidation: boolean = false;
    /// outputs

    /// fields

    /// injected dependencies
    protected logger: ILogger;
    protected validationService: IUserValidationService;


    /// ctor
    constructor(logger: ConsoleLogger, validationService: ExpertValidationService) {}

}