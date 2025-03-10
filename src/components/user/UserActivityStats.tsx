
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import { UserActivity } from "./UserTypes";

interface UserActivityStatsProps {
  activity: UserActivity;
}

const UserActivityStats = ({ activity }: UserActivityStatsProps) => {
  return (
    <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
      <Card>
        <CardHeader className="pb-2">
          <CardTitle className="text-sm font-medium">Polls Created</CardTitle>
        </CardHeader>
        <CardContent>
          <div className="text-2xl font-bold">{activity.poll_count}</div>
        </CardContent>
      </Card>
      
      <Card>
        <CardHeader className="pb-2">
          <CardTitle className="text-sm font-medium">Votes Cast</CardTitle>
        </CardHeader>
        <CardContent>
          <div className="text-2xl font-bold">{activity.vote_count}</div>
        </CardContent>
      </Card>
      
      <Card>
        <CardHeader className="pb-2">
          <CardTitle className="text-sm font-medium">Comments</CardTitle>
        </CardHeader>
        <CardContent>
          <div className="text-2xl font-bold">{activity.comment_count}</div>
        </CardContent>
      </Card>
      
      <Card>
        <CardHeader className="pb-2">
          <CardTitle className="text-sm font-medium">Ratings</CardTitle>
        </CardHeader>
        <CardContent>
          <div className="text-2xl font-bold">{activity.rating_count}</div>
        </CardContent>
      </Card>
    </div>
  );
};

export default UserActivityStats;
