import { Component, OnInit, Input, ViewChild } from '@angular/core';
import { Router } from '@angular/router';
import { ModalComponent } from 'ng2-bs3-modal/ng2-bs3-modal';
import { Variable, ConsoleLogger, ILogger } from './../../../utils/index';
import { IAuthorizationService, AuthorizationService } from './../../../services/index';
import { ISiteApiService, SiteApiService, GetAllResponse } from './../../../services/index';
import { ISiteEntityPolicyService, SiteEntityPolicyService } from './../../../services/index';
import { SiteEntity } from './../../../entities/index';
@Component({
    selector: 'site-cards',
    styleUrls: ['./siteCards.scss'],
    templateUrl: './siteCards.html'
})
export class SiteCardsComponent implements OnInit {
    /// inputs
    /// modals
    @ViewChild('siteDetailsModal')
    protected siteDetailsModal: ModalComponent;
    /// service fields
    private _siteDetailsModalMode: string;
    protected siteDetailsModalApplyPromise: Promise<void>;
    protected firstLoadPromise: Promise<void>;
    /// data fields
    protected items: Array<SiteEntity>;
    protected selectedEntity: SiteEntity;
    /// injected dependencies
    protected logger: ILogger;
    protected router: Router;
    protected authorizationManager: IAuthorizationService;
    protected siteApiService: ISiteApiService;
    protected siteEntityPolicy: ISiteEntityPolicyService;
    /// ctor
    constructor(
        logger: ConsoleLogger,
        router: Router,
        authorizationManager: AuthorizationService,
        siteApiService: SiteApiService,
        siteEntityPolicy: SiteEntityPolicyService) {
        this.logger = logger;
        this.router = router;
        this.authorizationManager = authorizationManager;
        this.siteApiService = siteApiService;
        this.siteEntityPolicy = siteEntityPolicy;
        logger.logDebug('SiteCardsComponent has been constructed.');
    }
    /// methods
    ngOnInit(): void {
        const self = this;
        self.firstLoadPromise = self
            .getManyEntities()
            .then(
                () => {
                    self.firstLoadPromise = null;
                },
                () => {
                    self.firstLoadPromise = null;
                });
    }
    protected getManyEntities(): Promise<void> {
        const self: SiteCardsComponent = this;
        const filter = {
            userId: this.authorizationManager.currentUserId
        };
        self._getManyPromise = self.siteApiService
            .getAll(0, 10, null, filter)
            .then(function (response: GetAllResponse<SiteEntity>): Promise<void> {
                self.items = response.items;
                self._getManyPromise = null;
                return Promise.resolve();
            })
            .then(
                () => {
                    self._getManyPromise = null;
                },
                () => {
                    // TODO: react on reason (error) here
                    self._getManyPromise = null;
                });
        return self._getManyPromise;
    }
    protected createEntity(entity: SiteEntity): Promise<SiteEntity> {
        const self = this;
        self._createPromise = self.siteApiService
            .create(entity)
            .then(
                (response: SiteEntity) => {
                    self._createPromise = null;
                    return Promise.resolve(response);
                },
                () => {
                    // TODO: react on reason (error) here
                    self._createPromise = null;
                });
        return self._createPromise;
    }
    protected updateEntity(entity: SiteEntity): Promise<SiteEntity> {
        const self = this;
        self._updateEntityId = entity.id;
        self._updatePromise = self.siteApiService
            .update(entity)
            .then(
                (response: SiteEntity) => {
                    self._updateEntityId = null;
                    self._updatePromise = null;
                    return Promise.resolve(response);
                },
                () => {
                    // TODO: react on reason (error) here
                    self._updateEntityId = null;
                    self._updatePromise = null;
                });
        return self._updatePromise;
    }
    protected deleteEntity(id: number): Promise<void> {
        const self = this;
        self._deleteEntityId = id;
        self._deletePromise = self.siteApiService
            .delete(id)
            .then(function (): Promise<void> {
                const elementIndex = self.items
                    .findIndex((item: SiteEntity) => item.id === id);
                if (elementIndex > -1) {
                    self.items.splice(elementIndex, 1);
                }
                return Promise.resolve();
            })
            .then(
                () => {
                    self._deleteEntityId = null;
                    self._deletePromise = null;
                },
                () => {
                    // TODO: react on reason (error) here
                    self._deleteEntityId = null;
                    self._deletePromise = null;
                });
        return self._deletePromise;
    }
    protected redirectToEntityDetails(entity: SiteEntity): Promise<boolean> {
        let actionPromise: Promise<boolean>;
        if (Variable.isNotNullOrUndefined(entity)) {
            actionPromise = this.router.navigate([`./pages/sites/${String(entity.id)}`]);
        } else {
            actionPromise = Promise.resolve(false);
        }
        return actionPromise;
    }
    protected selectEntity(entity: SiteEntity): Promise<SiteEntity> {
        const self = this;
        self.selectedEntity = self.items
            .find((item: SiteEntity) => item.id === entity.id);
        self._getEntityId = entity.id;
        self._getPromise = self.siteApiService
            .get(self.selectedEntity.id)
            .then(function (response: SiteEntity): Promise<void> {
                self.selectedEntity = response;
                return Promise.resolve();
            })
            .then(
                () => {
                    self._getEntityId = null;
                    self._getPromise = null;
                },
                () => {
                    self._getEntityId = null;
                    self._getPromise = null;
                });
        return self._getPromise;
    }
    protected openModalOnUpdate(entity): Promise<void> {
        const self = this;
        return self
            .selectEntity(entity)
            .then(function(selectedEntity: SiteEntity): Promise<void> {
                self._siteDetailsModalMode = 'Update';
                return self.siteDetailsModal.open();
            });
    }
    protected openModalOnCreate(): Promise<void> {
        this.selectedEntity = new SiteEntity();
        this.selectedEntity.userId = this.authorizationManager.currentUserId;
        this._siteDetailsModalMode = 'Create';
        return this.siteDetailsModal.open();
    }
    protected siteDetailsModalApply(): Promise<void> {
        const self = this;
        self.siteDetailsModalApplyPromise = (self._siteDetailsModalMode === 'Create' ?
                self.createEntity(self.selectedEntity) :
                self._siteDetailsModalMode === 'Update' ?
                    self.updateEntity(self.selectedEntity) :
                    Promise.resolve(self.selectedEntity))
                .then(function (response: SiteEntity): Promise<void> {
                    if (Variable.isNotNullOrUndefined(response)) {
                        const elementIndex = self.items
                            .findIndex((item: SiteEntity) => item.id === response.id);
                        if (elementIndex !== -1) {
                            self.items.splice(elementIndex, 1, response);
                        } else {
                            self.items.push(response);
                        }
                        self.selectedEntity = null;
                        self._siteDetailsModalMode = null;
                        return self.siteDetailsModal.close();
                    }
                })
                .then(
                    () => {
                        self.siteDetailsModalApplyPromise = null;
                    },
                    () => {
                        self.siteDetailsModalApplyPromise = null;
                    });
        return self.siteDetailsModalApplyPromise;
    }
    protected siteDetailsModalDismiss(): Promise<void> {
        this.selectedEntity = null;
        return this.siteDetailsModal.dismiss();
    }
    /// predicates
    protected isSiteDetailsModalApplyDisabled(): boolean {
        return Variable.isNotNullOrUndefined(this.siteDetailsModalApplyPromise) ||
            this.isAnyActionProcessing();
    }
    protected isSiteDetailsModalApplyProcessing(): boolean {
        return Variable.isNotNullOrUndefined(this.siteDetailsModalApplyPromise);
    }
    protected isSelectedEntityDefined(): boolean {
        return Variable.isNotNullOrUndefined(this.selectedEntity);
    }
    /// ---------------------------------------------------------------------------------------------------------------
    /// should be moved to action manager (or domain service)
    private _getManyPromise = null;
    private _getPromise: Promise<any> = null;
    private _getEntityId: number = null;
    private _createPromise: Promise<any> = null;
    private _updatePromise: Promise<any> = null;
    private _updateEntityId: number = null;
    private _deletePromise: Promise<any> = null;
    private _deleteEntityId: number = null;
    private isAnyActionProcessing(): boolean {
        return Variable.isNotNullOrUndefined(this._getManyPromise) ||
            Variable.isNotNullOrUndefined(this._getPromise) ||
            Variable.isNotNullOrUndefined(this._createPromise) ||
            Variable.isNotNullOrUndefined(this._updatePromise) ||
            Variable.isNotNullOrUndefined(this._deletePromise);
    }
    isActionGetManyAllowed(): boolean {
        return this.siteEntityPolicy.canGet();
    }
    isActionGetManyDisabled(): boolean {
        return this.isAnyActionProcessing();
    }
    isActionGetManyProcessing(): boolean {
        return Variable.isNotNullOrUndefined(this._getManyPromise);
    }
    isActionGetAllowed(entity): boolean {
        return this.siteEntityPolicy.canGetEntity(entity);
    }
    isActionGetDisabled(entity): boolean {
        return this.isAnyActionProcessing();
    }
    isActionGetProcessing(entity): boolean {
        return false;
        // return Variable.isNotNullOrUndefined(this._getPromise) &&
        //     Variable.isNotNullOrUndefined(entity) &&
        //     this._getEntityId === entity.id;
    }
    isActionCreateAllowed(): boolean {
        return this.siteEntityPolicy.canCreate();
    }
    isActionCreateDisabled(): boolean {
        return this.isAnyActionProcessing();
    }
    isActionCreateProcessing(): boolean {
        return Variable.isNotNullOrUndefined(this._createPromise);
    }
    isActionUpdateAllowed(entity): boolean {
        return this.siteEntityPolicy.canUpdateEntity(entity);
    }
    isActionUpdateDisabled(entity): boolean {
        return this.isAnyActionProcessing();
    }
    isActionUpdateProcessing(entity): boolean {
        return Variable.isNotNullOrUndefined(this._getPromise) &&
            Variable.isNotNullOrUndefined(entity) &&
            this._getEntityId === entity.id;
        // return Variable.isNotNullOrUndefined(this._updatePromise) &&
        //     Variable.isNotNullOrUndefined(entity) &&
        //     this._updateEntityId === entity.id;
    }
    isActionDeleteAllowed(entity): boolean {
        return this.siteEntityPolicy.canDeleteEntity(entity);
    }
    isActionDeleteDisabled(entity): boolean {
        return this.isAnyActionProcessing();
    }
    isActionDeleteProcessing(entity): boolean {
        return Variable.isNotNullOrUndefined(this._deletePromise) &&
            Variable.isNotNullOrUndefined(entity) &&
            this._deleteEntityId === entity.id;
    }
}
