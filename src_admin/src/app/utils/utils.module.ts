import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';

import { AuthorizationManager } from './auth/authorization.manager';
import { HttpService } from './http/http.service';
import { ConsoleLogger } from './logging/console/console.logger';

@NgModule({
    imports: [
        CommonModule,
        FormsModule
    ],
    declarations: [
    ],
    providers: [
        AuthorizationManager,
        HttpService,
        ConsoleLogger
    ]
})
export class UtilsModule {}