import { Component, OnInit, OnDestroy } from '@angular/core';
import { Extensions, IAuthorizationManager, AuthorizationManager } from './../../utils/index';
import { IUserApiService, UserApiService } from './../../services/serverApi/index';
@Component({
    selector: 'invitations',
    styleUrls: ['./invitations.scss'],
    templateUrl: 'invitations.html'
})
export class InvitationsComponent implements OnInit, OnDestroy {
    /// injected dependencies
    protected userApiService: IUserApiService;
    protected authorizationManager: IAuthorizationManager;
    /// ctor
    constructor(userApiService: UserApiService, authorizationManager: AuthorizationManager) {
        this.userApiService = userApiService;
        this.authorizationManager = authorizationManager;
    }
    /// methods
    ngOnInit(): void { }
    ngOnDestroy(): void { }
}
