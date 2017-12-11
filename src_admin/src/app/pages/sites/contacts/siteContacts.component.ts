import { Component, OnInit, Input, Output, EventEmitter, OnChanges, SimpleChanges, SimpleChange } from '@angular/core';
import { Variable, Extensions, ILogger, ConsoleLogger, PromiseService } from '../../../utils/index';
import { ISiteEntityPolicyService, SiteEntityPolicyService } from './../../../services/index';
import { ISiteValidationService, SiteValidationService } from './../../../services/index';
/// is used like entity for contacts (internal model)
class ContactEntity {
    value: string;
    editValue: string;
    isEditProcessing: boolean;
    /// ctor
    constructor(value: string) {
        this.value = value;
        this.editValue = this.value;
        this.isEditProcessing = false;
    }
    /// methods
    startEdit(): void {
        this.isEditProcessing = true;
    }
    commitEdit(): void {
        this.value = this.editValue;
        this.isEditProcessing = false;
    }
    undoEdit(): void {
        this.isEditProcessing = false;
    }
}
@Component({
    selector: 'site-contacts',
    styleUrls: ['./siteContacts.scss'],
    templateUrl: './siteContacts.html',
})
export class SiteContactsComponent implements OnInit, OnChanges {
    /// inputs
    @Input() siteContacts: string;
    @Input() isReadOnly: boolean = false;
    /// outputs
    @Output() onSaveSiteContacts: EventEmitter<string> = new EventEmitter<string>();
    /// fields
    private _contacts: string = '';
    protected emailEntities: Array<ContactEntity>;
    protected newEmailEntity: ContactEntity;
    protected smsEntities: Array<ContactEntity>;
    protected newSMSEntity: ContactEntity;
    /// injected dependencies
    protected logger: ILogger;
    protected promiseService: PromiseService;
    protected policyService: ISiteEntityPolicyService;
    protected validationService: ISiteValidationService;
    /// ctor
    constructor(
        logger: ConsoleLogger,
        promiseService: PromiseService,
        policyService: SiteEntityPolicyService,
        validationService: SiteValidationService) {
        this.logger = logger;
        this.promiseService = promiseService;
        this.policyService = policyService;
        this.validationService = validationService;
        this.logger.logDebug('SiteContactsComponent: Component has been constructed.');
    }
    /// methods
    ngOnInit(): void {
        this.initializeNotificationEntities();
    }
    ngOnChanges(changes: SimpleChanges) {
        const siteContactsChange: SimpleChange = changes['siteContacts'];
        if (Variable.isNotNullOrUndefined(siteContactsChange) &&
            this._contacts !== this.siteContacts) {
            this._contacts = this.siteContacts;
            this.initializeNotificationEntities();
        }
    }
    protected initializeNotificationEntities() {
        this.initializeEmailEntities();
        this.initializeSMSEntities();
    }
    protected saveSiteContacts(): void {
        this.onSaveSiteContacts.emit(`${this.emailEntities.map((item) => item.value).join(',')};${this.smsEntities.map((item) => item.value).join(',')}`);
    }
    protected resetSiteContacts(): void {
        this.initializeNotificationEntities();
    }
    // email contacts
    protected initializeEmailEntities(): void {
        this.emailEntities = this.parseContactEntitiesString(this._contacts, ';', ',', 0);
        this.newEmailEntity = new ContactEntity('');
    }
    protected deleteEmailFromContacts(emailEntity: any): void {
        const index = this.emailEntities.findIndex((r) => r === emailEntity);
        if (index > -1) {
            this.emailEntities.splice(index, 1);
        }
    }
    protected addNewEmailEntity(): void {
        this.emailEntities.push(new ContactEntity(this.newEmailEntity.value));
        this.newEmailEntity.value = '';
    }
    // sms contacts
    protected initializeSMSEntities(): void {
        this.smsEntities = this.parseContactEntitiesString(this._contacts, ';', ',', 1);
        this.newSMSEntity = new ContactEntity('');
    }
    protected deleteSMSFromContacts(smsEntity: any): void {
        const index = this.smsEntities.findIndex((r) => r === smsEntity);
        if (index > -1) {
            this.smsEntities.splice(index, 1);
        }
    }
    protected addNewSMSEntity(): void {
        this.smsEntities.push(new ContactEntity(this.newSMSEntity.value));
        this.newSMSEntity.value = '';
    }
    /// predicates
    protected isEmailValid(value: string): boolean {
        return Extensions.regExp.email.test(value) && !this.containsContactsEntityValue(this.emailEntities, value);
    }
    protected isSMSValid(value: string): boolean {
        return Extensions.regExp.phoneNumber.test(value) && !this.containsContactsEntityValue(this.smsEntities, value);
    }
    protected isSaveProcessing(): boolean {
        return this.isReadOnly;
    }
    private containsContactsEntityValue(collection: Array<ContactEntity>, value: string): boolean {
        return Variable.isNullOrUndefined(collection) || Variable.isNullOrUndefined(collection.length) ||
            collection.findIndex((item: ContactEntity) => item.value === value) > -1;
    }
    /// helpers
    private parseContactEntitiesString(str: string, globalSeparator: string, localSeparator: string, globalPosition: number): Array<ContactEntity> {
        const result = [];
        if (str && str.indexOf(globalSeparator) > -1) {
            let arr = str.split(globalSeparator);
            if (arr.length > globalPosition && arr[globalPosition] !== '') {
                let values: Array<string> = arr[globalPosition].split(localSeparator);
                for (let item of values) {
                    result.push(new ContactEntity(item));
                }
            }
        }
        return result
    }
}