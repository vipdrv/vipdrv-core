import { SiteEntity } from './../../../../../entities/widget/sites/site.entity';
import { IValidationService } from './../../../i-validation-service';
export interface ISiteValidationService extends IValidationService<SiteEntity> {
    isNameValid(site: SiteEntity): boolean;
    isUrlValid(site: SiteEntity): boolean;
    getInvalidNameMessageKey(site: SiteEntity): string;
    getInvalidUrlMessageKey(site: SiteEntity): string;
}