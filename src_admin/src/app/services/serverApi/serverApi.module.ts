import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';

import { UserApiService } from './main/users/user.api-service';

import { BeverageApiService } from './widget/beverages/beverage.api-service';
import { ExpertApiService } from './widget/experts/expert.api-service';
import { LeadApiService } from './widget/leads/lead.api-service';
import { RouteApiService } from './widget/routes/route.api-service';
import { SiteApiService } from './widget/sites/site.api-service';
import { WidgetThemeApiService } from './widget/themes/widgetTheme.api-service';

@NgModule({
    imports: [
        CommonModule
    ],
    declarations: [],
    providers: [
        UserApiService,
        BeverageApiService,
        ExpertApiService,
        LeadApiService,
        RouteApiService,
        SiteApiService,
        WidgetThemeApiService
    ]
})
export class ServerApiModule {}