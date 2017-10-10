import { Component, OnInit } from '@angular/core';
import { Router } from '@angular/router';

import { IAuthorizationManager, AuthorizationManager } from './../../utils/index';

@Component({
    selector: 'logout',
    styleUrls: ['./logout.scss'],
    templateUrl: 'logout.html'
})
export class LogoutComponent implements OnInit {
    protected authorizationManager: IAuthorizationManager;

    constructor(
        private router: Router,
        authorizationManager: AuthorizationManager) {
        this.authorizationManager = authorizationManager;
    }

    ngOnInit() {
        let self = this;
        self.authorizationManager
            .signOut()
            .then(function () {
                self.router.navigate(['/']);
            });
    }
}
