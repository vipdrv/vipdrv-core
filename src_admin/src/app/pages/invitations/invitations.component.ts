import { Component, OnInit, OnDestroy } from '@angular/core';
import { Extensions } from './../../utils/index';
import { IUserApiService, UserApiService } from './../../services/serverApi/index';
@Component({
    selector: 'invitations',
    styleUrls: ['./invitations.scss'],
    templateUrl: 'invitations.html'
})
export class InvitationsComponent implements OnInit, OnDestroy {
    /// injected dependencies
    protected userApiService: IUserApiService;
    /// ctor
    constructor(userApiService: UserApiService) {
        this.userApiService = userApiService;
    }
    /// methods
    ngOnInit(): void { }
    ngOnDestroy(): void { }
}
