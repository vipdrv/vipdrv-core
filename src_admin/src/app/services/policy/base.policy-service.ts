import { Injectable } from '@angular/core';

import { AuthorizationService } from './../authorization/index';

@Injectable()
export class BasePolicyService {
    /// injected dependencies
    protected authService: AuthorizationService;
    /// ctor
    constructor(authService: AuthorizationService) {
        this.authService = authService;
    }
    /// methods
}