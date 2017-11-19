import { Component } from '@angular/core';
import { Variable, ConsoleLogger, ILogger } from './../../utils/index';
@Component({
    selector: 'user-profile',
    styleUrls: ['./userProfile.scss'],
    templateUrl: './userProfile.html',
})
export class UserProfileComponent {
    /// ctor
    constructor() {

    }

    /// personal info

    /// avatar

    /// change password
    private _changePasswordPromise: Promise<void>;
    protected oldPassword: string;
    protected newPassword: string;
    protected newRepeatedPassword: string;
    isAnyPasswordInputDisabled(): boolean {
        return this.isCommitPasswordChangeProcessing();
    }
    isCommitPasswordChangeProcessing(): boolean {
        return Variable.isNotNullOrUndefined(this._changePasswordPromise);
    }
    isCommitPasswordChangeDisabled(): boolean {
        return Variable.isNotNullOrUndefined(this._changePasswordPromise) ||
            Variable.isNullOrUndefined(this.oldPassword) ||
            Variable.isNullOrUndefined(this.newPassword) ||
            this.newPassword !== this.newRepeatedPassword;
    }
    commitPasswordChange(): Promise<void> {
        return Promise.resolve();
    }
}
