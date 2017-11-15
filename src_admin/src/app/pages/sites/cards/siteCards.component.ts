import { Component, OnInit, Input, ViewChild } from '@angular/core';
import { Router } from '@angular/router';
import { ModalComponent } from 'ng2-bs3-modal/ng2-bs3-modal';
import { Variable, PromiseService, ConsoleLogger, ILogger } from './../../../utils/index';
import { IAuthorizationService, AuthorizationService } from './../../../services/index';
import { ISiteApiService, SiteApiService, GetAllResponse } from './../../../services/index';
import { SiteEntity } from './../../../entities/index';
@Component({
    selector: 'site-cards',
    styleUrls: ['./siteCards.scss'],
    templateUrl: './siteCards.html'
})
export class SiteCardsComponent implements OnInit {
    /// inputs
    /// modals
    /// settings
    /// data fields
    protected items: Array<SiteEntity>;
    /// injected dependencies
    protected logger: ILogger;
    protected authorizationManager: IAuthorizationService;
    protected siteApiService: ISiteApiService;
    protected promiseService: PromiseService;
    protected router: Router;
    /// ctor
    constructor(
        logger: ConsoleLogger,
        authorizationManager: AuthorizationService,
        siteApiService: SiteApiService,
        promiseService: PromiseService,
        router: Router) {
        this.logger = logger;
        this.authorizationManager = authorizationManager;
        this.siteApiService = siteApiService;
        this.promiseService = promiseService;
        this.router = router;
    }
    /// methods
    ngOnInit(): void {
        const self: SiteCardsComponent = this;
        const filter = {
            userId: this.authorizationManager.lastUser.userId
        };
        self.promiseService.applicationPromises.sites.getAll.promise = self.siteApiService
            .getAll(0, 10, null, filter)
            .then(function (response: GetAllResponse<SiteEntity>): Promise<void> {
                self.items = response.items;
                return Promise.resolve();
            });
    }
}
