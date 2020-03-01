import { Component, Input, Output, EventEmitter } from '@angular/core';
import { InvitationEntity, RoleEntity } from './../../../../entities/index';
import { Extensions, ILogger, ConsoleLogger } from './../../../../utils/index';
import { IInvitationValidationService, InvitationValidationService } from './../../../../services/index';
@Component({
    selector: 'invitation-details-create',
    styleUrls: ['./invitationDetailsCreate.scss'],
    templateUrl: './invitationDetailsCreate.html',
})
export class InvitationDetailsCreateComponent {
    /// inputs
    @Input() entity: InvitationEntity;
    @Input() rolesCanBeUsedForInvitation: Array<RoleEntity> = [];
    @Input() useValidation: boolean = false;
    @Input() isProcessing: boolean = false;
    /// outputs
    @Output() onEntityChange: EventEmitter<InvitationEntity> = new EventEmitter<InvitationEntity>();
    /// injected dependencies
    protected extensions = Extensions;
    protected logger: ILogger;
    protected validationService: IInvitationValidationService;
    /// ctor
    constructor(
        logger: ConsoleLogger,
        validationService: InvitationValidationService) {
        this.logger = logger;
        this.validationService = validationService;
        this.logger.logDebug('RegistrationComponent: Component has been constructed.');
    }
    /// methods
    protected onChangeEmail(newValue: string): void {
        if (this.entity.email !== newValue) {
            this.entity.email = newValue;
            this.notifyOnEntityChange();
        }
    }
    protected onChangeRole(): void {
        this.notifyOnEntityChange();
    }
    protected onChangeSiteCount(newValue: number): void {
        if (this.entity.availableSitesCount !== newValue) {
            this.entity.availableSitesCount = newValue;
            this.notifyOnEntityChange();
        }
    }
    protected notifyOnEntityChange() {
        this.onEntityChange.emit(this.entity);
    }
    /// predicates
    protected isValidationActive(): boolean {
        return this.useValidation;
    }
    protected isFormProcessing(): boolean {
        return this.isProcessing;
    }
}