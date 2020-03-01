import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { ContentApiService } from './content/content.api-service';
import { UserApiService } from './main/users/user.api-service';
import { RoleApiService } from './main/roles/role.api-service';
import { BeverageApiService } from './widget/beverages/beverage.api-service';
import { ExpertApiService } from './widget/experts/expert.api-service';
import { LeadApiService } from './widget/leads/lead.api-service';
import { RouteApiService } from './widget/routes/route.api-service';
import { SiteApiService } from './widget/sites/site.api-service';
import { WidgetThemeApiService } from './widget/themes/widgetTheme.api-service';
import { StepApiService } from './widget/steps/step.api-service';
@NgModule({
    imports: [
        CommonModule
    ],
    declarations: [],
    providers: [
        ContentApiService,
        UserApiService,
        RoleApiService,
        BeverageApiService,
        ExpertApiService,
        LeadApiService,
        RouteApiService,
        SiteApiService,
        WidgetThemeApiService,
        StepApiService
    ]
})
export class ServerApiModule {}