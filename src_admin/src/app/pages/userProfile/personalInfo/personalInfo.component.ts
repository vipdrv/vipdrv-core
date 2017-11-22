import { Component, Input, OnInit } from '@angular/core';
import { Variable, ILogger, ConsoleLogger } from './../../../utils/index';
import { UserEntity } from './../../../entities/index';
import { IUserApiService, UserApiService } from './../../../services/index';
@Component({
    selector: 'user-personal-info',
    styleUrls: ['./personalInfo.scss'],
    templateUrl: './personalInfo.html',
})
export class PersonalInfoComponent implements OnInit {
    /// inputs
    @Input() user: UserEntity;
    /// service fields
    /// fields
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

    }
    /// predicates
    isUserDefined(): boolean {
        return Variable.isNotNullOrUndefined(this.user);
    }
    /// helpers
}
