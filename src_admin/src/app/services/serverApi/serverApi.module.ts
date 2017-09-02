import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';

import { SiteApiService } from './site/site.api-service';

@NgModule({
    imports: [
        CommonModule
    ],
    declarations: [],
    providers: [
        SiteApiService
    ]
})
export class ServerApiModule {}