import { Component, OnInit } from '@angular/core';
import { Router } from '@angular/router';
import { IAuthorizationService, AuthorizationService } from './../../services/index';

@Component({
    selector: 'logout',
    styleUrls: ['./logout.scss'],
    templateUrl: 'logout.html'
})
export class LogoutComponent implements OnInit {
    protected authorizationManager: IAuthorizationService;

    constructor(
        private router: Router,
        authorizationManager: AuthorizationService) {
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
