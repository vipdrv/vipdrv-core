import { Component, Input, Output, EventEmitter } from '@angular/core';
import { Variable, ILogger, ConsoleLogger } from './../../../utils/index';
import { IUserApiService, UserApiService } from './../../../services/index';
@Component({
    selector: 'user-password-update',
    styleUrls: ['./passwordUpdate.scss'],
    templateUrl: './passwordUpdate.html',
})
export class PasswordUpdateComponent {
    /// inputs
    @Input() userId: number;
    /// outputs
    @Output() passwordPatched: EventEmitter<any> = new EventEmitter<any>();
    /// service fields
    protected patchPasswordPromise: Promise<void>;
    /// fields
    protected oldPassword: string;
    protected newPassword: string;
    protected newRepeatedPassword: string;
    /// injected dependencies
    protected logger: ILogger;
    protected userApiService: IUserApiService;
    /// ctor
    constructor(logger: ConsoleLogger, userApiService: UserApiService) {
        this.logger = logger;
        this.userApiService = userApiService;
        this.logger.logDebug('PasswordUpdateComponent: Component has been constructed.');
    }
    /// methods
    commitPasswordChange(): Promise<void> {
        const self = this;
        self.patchPasswordPromise = self.userApiService
            .patchPassword(self.userId, self.oldPassword, self.newPassword)
            .then(function() {
                self.oldPassword = null;
                self.newPassword = null;
                self.newRepeatedPassword = null;
                self.passwordPatched.emit();
            })
            .then(
                () => {
                    self.patchPasswordPromise = null;
                },
                () => {
                    self.patchPasswordPromise = null;
                },
            );
        return self.patchPasswordPromise;
    }
    /// predicates
    isUserIdDefined(): boolean {
        return Variable.isNotNullOrUndefined(this.userId);
    }
    isAnyPasswordInputDisabled(): boolean {
        return this.isCommitPasswordChangeProcessing();
    }
    isCommitPasswordChangeProcessing(): boolean {
        return Variable.isNotNullOrUndefined(this.patchPasswordPromise);
    }
    isCommitPasswordChangeDisabled(): boolean {
        return Variable.isNotNullOrUndefined(this.patchPasswordPromise) ||
            Variable.isNullOrUndefined(this.oldPassword) ||
            Variable.isNullOrUndefined(this.newPassword) ||
            this.newPassword !== this.newRepeatedPassword;
    }
}
