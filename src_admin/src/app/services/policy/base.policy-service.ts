import { Injectable } from '@angular/core';
import { IAuthorizationService, AuthorizationService } from './../index';
@Injectable()
export class BasePolicyService {
    /// injected dependencies
    protected authService: IAuthorizationService;
    /// ctor
    constructor(authService: AuthorizationService) {
        this.authService = authService;
    }
    /// methods
}