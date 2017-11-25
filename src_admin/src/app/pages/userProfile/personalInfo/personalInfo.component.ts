import { Component, Input, Output, EventEmitter, OnInit } from '@angular/core';
import { Variable, Extensions, ILogger, ConsoleLogger } from './../../../utils/index';
import { UserEntity } from './../../../entities/index';
import { IUserApiService, UserApiService } from './../../../services/index';
const enum ComponentMode {
    View = 1,
    Edit = 2
}
@Component({
    selector: 'user-personal-info',
    styleUrls: ['./personalInfo.scss'],
    templateUrl: './personalInfo.html',
})
export class PersonalInfoComponent implements OnInit {
    /// inputs
    @Input() user: UserEntity;
    /// outputs
    @Output() personalInfoPatched: EventEmitter<any> = new EventEmitter<any>();
    /// service fields
    private _componentMode: ComponentMode = ComponentMode.View;
    private _patchPersonalInfoPromise: Promise<void>;
    /// fields
    protected newPersonalInfo: any = {
        firstName: null,
        secondName: null,
        email: null,
        phoneNumber: null
    };
    /// injected dependencies
    protected logger: ILogger;
    protected userApiService: IUserApiService;
    /// ctor
    constructor(
        logger: ConsoleLogger,
        userApiService: UserApiService) {
        this.logger = logger;
        this.userApiService = userApiService;
        this.logger.logDebug('PersonalInfoComponent: Component has been constructed.');
    }
    /// methods
    ngOnInit(): void {
        this.setModeView();
    }
    patchPersonalInfo(): Promise<void> {
        const self = this;
        self.correctPersonalInfo();
        self._patchPersonalInfoPromise = self.userApiService
            .patchPersonalInfo(
                self.user.id,
                self.newPersonalInfo.firstName,
                self.newPersonalInfo.secondName,
                self.newPersonalInfo.email,
                self.newPersonalInfo.phoneNumber)
            .then(function() {
                self.logger.logDebug('PersonalInfoComponent: Personal info has been patched.');
                if (self.personalInfoPatched) {
                    self.personalInfoPatched.emit(self.newPersonalInfo);
                }
                self.setModeView();
            })
            .then(
                () => {
                    self._patchPersonalInfoPromise = null;
                },
                () => {
                    self._patchPersonalInfoPromise = null;
                },
            );
        return self._patchPersonalInfoPromise;
    }
    setModeView(): void {
        this._componentMode = ComponentMode.View;
        this.initializeNewPersonalInfoModel();
    }
    setModeEdit(): void {
        this._componentMode = ComponentMode.Edit;
    }
    /// predicates
    isUserDefined(): boolean {
        return Variable.isNotNullOrUndefined(this.user);
    }
    isComponentModeView(): boolean {
        return this._componentMode === ComponentMode.View;
    }
    isComponentModeEdit(): boolean {
        return this._componentMode === ComponentMode.Edit;
    }
    isNewPersonalInfoValid(): boolean {
        return Variable.isNotNullOrUndefined(this.newPersonalInfo) &&
            Variable.isNotNullOrUndefined(this.newPersonalInfo.firstName) &&
            this.newPersonalInfo.firstName !== '' &&
            Variable.isNotNullOrUndefined(this.newPersonalInfo.secondName) &&
            this.newPersonalInfo.secondName !== '' &&
            Extensions.regExp.email.test(this.newPersonalInfo.email) &&
            Extensions.regExp.phoneNumber.test(this.newPersonalInfo.phoneNumber);
    }
    isPatchPersonalInfoProcessing(): boolean {
        return Variable.isNotNullOrUndefined(this._patchPersonalInfoPromise);
    }
    isCommitChangesDisabled(): boolean {
        return this.isPatchPersonalInfoProcessing() ||
            !this.isNewPersonalInfoValid();
    }
    /// helpers
    private initializeNewPersonalInfoModel(): void {
        this.newPersonalInfo.firstName = this.user.firstName;
        this.newPersonalInfo.secondName = this.user.secondName;
        this.newPersonalInfo.email = this.user.email;
        this.newPersonalInfo.phoneNumber = this.user.phoneNumber;
    }
    private correctPersonalInfo(): void {
        this.newPersonalInfo.firstName.trim();
        this.newPersonalInfo.secondName.trim();
        // phone number correction
        if (Variable.isNullOrUndefined(this.newPersonalInfo.phoneNumber)) {
            this.newPersonalInfo.phoneNumber = null;
        } else {
            this.newPersonalInfo.phoneNumber.trim();
            if (this.newPersonalInfo.phoneNumber === '') {
                this.newPersonalInfo.phoneNumber = null;
            }
        }
    }
}
