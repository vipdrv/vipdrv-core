import { Component, OnInit, OnDestroy } from '@angular/core';
import { Extensions, Variable, IAuthorizationManager, AuthorizationManager } from './../../utils/index';
import { IUserApiService, UserApiService } from './../../services/serverApi/index';
import { InvitationEntity } from './../../entities/index';
@Component({
    selector: 'invitations',
    styleUrls: ['./invitations.scss'],
    templateUrl: 'invitations.html'
})
export class InvitationsComponent implements OnInit, OnDestroy {
    protected model = {
        email: null,
        phoneNumber: null,
        availableSitesCount: 0,
        roleId: 1
    };
    /// injected dependencies
    protected userApiService: IUserApiService;
    protected authorizationManager: IAuthorizationManager;
    private _sendInvitationPromise = null;
    /// ctor
    constructor(userApiService: UserApiService, authorizationManager: AuthorizationManager) {
        this.userApiService = userApiService;
        this.authorizationManager = authorizationManager;
    }
    /// methods
    ngOnInit(): void { }
    ngOnDestroy(): void { }
    sendInvitation(): void {
        const self = this;
        const entity = new InvitationEntity();
        entity.email = self.model.email;
        entity.phoneNumber = self.model.phoneNumber;
        entity.availableSitesCount = self.model.availableSitesCount;
        entity.roleId = self.model.roleId;
        self._sendInvitationPromise = self.userApiService
            .createInvitation(self.authorizationManager.lastUser.userId, entity)
            .then(function(response: InvitationEntity): void {
                self._sendInvitationPromise = null;
            }).catch(function (reason) {
                self._sendInvitationPromise = null;
            });
    }
    isSendInvitationDisabled(): boolean {
        return this.isSendInvitationInProgress() ||
            !this.isValidEmail(this.model.email);
    }
    isSendInvitationInProgress(): boolean {
        return Variable.isNotNullOrUndefined(this._sendInvitationPromise);
    }
    protected isValidEmail(value: string): boolean {
        return Extensions.regExp.email.test(value)
    }
}
